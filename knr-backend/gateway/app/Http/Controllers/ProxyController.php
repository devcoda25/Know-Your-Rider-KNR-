<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Forwards /api/v1/{resource}/{path} to {upstream}/api/{resource}/{path}.
 *
 * Route defaults supply `upstream` and `resource`; the wildcard segment arrives as
 * `path` (or `action` for the explicit auth routes).
 */
class ProxyController extends Controller
{
    public function forward(Request $request): Response
    {
        $upstream = (string) $request->route('upstream');
        $resource = trim((string) $request->route('resource'), '/');
        $path = trim((string) ($request->route('path') ?? $request->route('action') ?? ''), '/');

        $base = rtrim((string) config("gateway.upstreams.$upstream"), '/');
        abort_if($base === '', 502, 'Unknown upstream service.');

        $url = $base.'/api/'.$resource.($path !== '' ? '/'.$path : '');

        try {
            $response = $this->send($request, $this->client($request), $url);
        } catch (ConnectionException) {
            return response()->json(['message' => "The $upstream service is unavailable."], 503);
        }

        return response($response->body(), $response->status())
            ->header('Content-Type', $response->header('Content-Type') ?: 'application/json');
    }

    private function client(Request $request): PendingRequest
    {
        // Only an allow-list of client headers is forwarded. Identity headers are
        // built from the verified token, never copied from the incoming request.
        $headers = [
            'Accept' => 'application/json',
            'X-Internal-Key' => (string) config('gateway.internal_key'),
            'X-Request-Id' => $request->header('X-Request-Id') ?: (string) Str::uuid(),
            'X-Forwarded-For' => (string) $request->ip(),
        ];

        foreach (['Accept-Language', 'X-Device-Id'] as $name) {
            if ($request->hasHeader($name)) {
                $headers[$name] = (string) $request->header($name);
            }
        }

        if ($user = $request->attributes->get('gateway.user')) {
            $headers['X-User-Id'] = $user['id'];
            $headers['X-User-Roles'] = json_encode($user['roles']);
        }

        return Http::withHeaders($headers)
            ->connectTimeout(5)
            ->timeout((int) config('gateway.timeout', 15));
    }

    private function send(Request $request, PendingRequest $client, string $url)
    {
        $query = $request->getQueryString();
        $fullUrl = $url.($query ? '?'.$query : '');
        $files = $request->allFiles();

        // Photo uploads (rider and bike photos): top-level files only, POST only.
        if ($files && $request->isMethod('POST')) {
            foreach ($files as $name => $file) {
                if (! is_array($file)) {
                    $client = $client->attach($name, fopen($file->getRealPath(), 'r'), $file->getClientOriginalName());
                }
            }

            return $client->post($fullUrl, $request->except(array_keys($files)));
        }

        if (in_array($request->method(), ['GET', 'HEAD', 'DELETE'], true)) {
            return $client->send($request->method(), $fullUrl);
        }

        return $client
            ->withBody($request->getContent(), $request->header('Content-Type', 'application/json'))
            ->send($request->method(), $fullUrl);
    }
}
