<?php

namespace App\Http\Controllers;

use App\Models\Bike;
use App\Models\Rider;
use App\Support\CurrentUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

/** Photos are uploaded one at a time as multipart (field name: "photo"). */
class PhotoController extends Controller
{
    public function rider(Request $request, string $rider): JsonResponse
    {
        $rider = Rider::findOrFail($rider);
        abort_unless(CurrentUser::fromRequest($request)->canManageStage($rider->stage_id), 403);

        $path = $this->store($request, "riders/{$rider->id}", $rider->photo_path);
        $rider->update(['photo_path' => $path]);

        return response()->json(['photo_path' => $path]);
    }

    public function bike(Request $request, string $bike): JsonResponse
    {
        $bike = Bike::with('rider')->findOrFail($bike);
        abort_unless(CurrentUser::fromRequest($request)->canManageStage($bike->rider->stage_id), 403);

        $path = $this->store($request, "bikes/{$bike->id}", $bike->photo_path);
        $bike->update(['photo_path' => $path]);

        return response()->json(['photo_path' => $path]);
    }

    private function store(Request $request, string $directory, ?string $previous): string
    {
        $request->validate(['photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096']]);

        $path = $request->file('photo')->store($directory, 'public');

        if ($previous) {
            Storage::disk('public')->delete($previous);
        }

        return $path;
    }
}
