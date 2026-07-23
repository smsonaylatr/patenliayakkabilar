<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PoregoWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $signature = $request->header('X-Porego-Signature');
        $secret = config('services.porego.webhook_secret', env('POREGO_WEBHOOK_SECRET'));

        if (!$secret) {
            Log::error('Porego Webhook Secret is not configured.');
            return response()->json(['error' => 'Webhook secret not configured'], 500);
        }

        if (!$signature) {
            Log::error('Porego Webhook Signature is missing.');
            return response()->json(['error' => 'Signature missing'], 401);
        }

        $payload = $request->getContent();
        $expectedSignature = hash_hmac('sha256', $payload, $secret);

        if (!hash_equals($expectedSignature, $signature)) {
            Log::error('Porego Webhook Signature verification failed.', [
                'expected' => $expectedSignature,
                'actual' => $signature,
            ]);
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = $request->json()->all();

        // Process the webhook data here
        Log::info('Porego Webhook Received:', $data);

        // TODO: Update order status based on webhook payload
        // Example: if ($data['event'] === 'order.status.updated') { ... }

        return response()->json(['status' => 'success']);
    }
}
