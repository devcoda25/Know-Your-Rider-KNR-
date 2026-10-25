<?php

namespace App\Http\Controllers;

use App\Http\Resources\RiderFullResource;
use App\Models\Bike;
use App\Models\Rider;
use App\Models\Stage;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RiderController extends Controller
{
    private const WITH = ['stage', 'bike', 'documents', 'incidents'];

    public function index(Request $request)
    {
        $user = CurrentUser::fromRequest($request);

        $riders = Rider::visibleTo($user)
            ->with(self::WITH)
            ->when($request->query('stage_id'), fn ($q, $id) => $q->where('stage_id', $id))
            ->when($request->query('q'), function ($q, $term) {
                $plate = Bike::normalizePlate($term);
                $q->where(fn ($w) => $w
                    ->where('full_name', 'ilike', "%{$term}%")
                    ->orWhereHas('bike', fn ($b) => $b->where('plate_normalized', 'like', "%{$plate}%")));
            })
            ->orderBy('full_name')
            ->paginate(25);

        // Status filtering is computed from documents, so apply it after loading the page.
        // TODO: move to a stored/cached status column once lists get large.
        if ($status = $request->query('status')) {
            $riders->setCollection($riders->getCollection()->filter(fn ($r) => $r->complianceStatus() === $status)->values());
        }

        return RiderFullResource::collection($riders);
    }

    public function store(Request $request): JsonResponse
    {
        $user = CurrentUser::fromRequest($request);

        $data = $request->validate([
            'stage_id' => ['required', 'string', 'exists:stages,id'],
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'regex:/^\+256\d{9}$/'],
            'national_id' => ['required', 'string', 'max:30'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'address' => ['nullable', 'string', 'max:200'],
            'next_of_kin_name' => ['nullable', 'string', 'max:120'],
            'next_of_kin_phone' => ['nullable', 'regex:/^\+256\d{9}$/'],
            'consent' => ['required', 'accepted'],

            'bike.plate' => ['required', 'string', 'max:20'],
            'bike.make' => ['required', 'string', 'max:60'],
            'bike.model' => ['nullable', 'string', 'max:60'],
            'bike.color' => ['nullable', 'string', 'max:40'],

            'documents' => ['nullable', 'array', 'max:6'],
            'documents.*.type' => ['required', 'in:permit,insurance'],
            'documents.*.number' => ['nullable', 'string', 'max:60'],
            'documents.*.issuer' => ['nullable', 'string', 'max:120'],
            'documents.*.expires_at' => ['required', 'date'],
        ]);

        abort_unless($user->canManageStage($data['stage_id']), 403, 'You cannot register riders for this stage.');

        $stage = Stage::findOrFail($data['stage_id']);
        abort_unless($stage->status === 'active', 422, 'This stage is awaiting approval.');
        // TODO: enforce the free-tier rider limit / active subscription for the stage here.

        $hash = Rider::hashNationalId($data['national_id']);
        if (Rider::where('national_id_hash', $hash)->exists()) {
            return response()->json(['message' => 'A rider with this National ID is already registered.'], 422);
        }

        if (Bike::where('plate_normalized', Bike::normalizePlate($data['bike']['plate']))->exists()) {
            return response()->json(['message' => 'This number plate is already registered.'], 422);
        }

        $rider = DB::transaction(function () use ($data, $user, $hash) {
            $rider = Rider::create([
                ...collect($data)->only([
                    'stage_id', 'full_name', 'phone', 'national_id', 'date_of_birth', 'address',
                    'next_of_kin_name', 'next_of_kin_phone',
                ])->all(),
                'national_id_hash' => $hash,
                'consent_at' => now(),
                'registered_by' => $user->id,
            ]);

            $rider->bike()->create($data['bike']);

            foreach ($data['documents'] ?? [] as $document) {
                $rider->documents()->create($document);
            }

            return $rider;
        });

        return (new RiderFullResource($rider->load(self::WITH)))
            ->response()
            ->setStatusCode(201);
    }

    public function show(Request $request, string $rider): RiderFullResource
    {
        $user = CurrentUser::fromRequest($request);

        return new RiderFullResource(
            Rider::visibleTo($user)->with(self::WITH)->findOrFail($rider)
        );
    }

    public function suspend(Request $request, string $rider): RiderFullResource
    {
        $data = $request->validate(['reason' => ['required', 'string', 'max:200']]);

        $rider = $this->manageable($request, $rider);
        $rider->update(['status' => 'suspended', 'suspension_reason' => $data['reason']]);

        return new RiderFullResource($rider->load(self::WITH));
    }

    public function reinstate(Request $request, string $rider): RiderFullResource
    {
        $rider = $this->manageable($request, $rider);
        $rider->update(['status' => 'active', 'suspension_reason' => null]);

        return new RiderFullResource($rider->load(self::WITH));
    }

    private function manageable(Request $request, string $id): Rider
    {
        $rider = Rider::findOrFail($id);
        abort_unless(CurrentUser::fromRequest($request)->canManageStage($rider->stage_id), 403);

        return $rider;
    }
}
