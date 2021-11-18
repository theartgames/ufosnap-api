<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Key;
use Illuminate\Http\Request;

class AuthorizeApiKey
{
    const AUTH_HEADER = 'PDF_SECRET';

    public function handle(Request $request, Closure $next)
    {
        return $next($request);

        $header = $request->header(self::AUTH_HEADER);
        $apiKey = Key::getByKey($header);

        if ($apiKey instanceof ApiKey) {
            // $this->logAccessEvent($request, $apiKey);
            return $next($request);
        }

        return response([
            'errors' => [[
                'message' => 'Unauthorized'
            ]]
        ], 401);
    }

    /*
    protected function logAccessEvent(Request $request, ApiKey $apiKey)
    {
        $event = new ApiKeyAccessEvent;
        $event->api_key_id = $apiKey->id;
        $event->ip_address = $request->ip();
        $event->url        = $request->fullUrl();
        $event->save();
    }
    */
}
