<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class N8nWebhookController extends Controller
{
    public function publishBlog(Request $request)
    {
        try {
            // Check API Key
            $apiKey = env('N8N_API_KEY', 'patenli_n8n_secret_123'); // Varsayılan bir key
            if ($request->header('Authorization') !== 'Bearer ' . $apiKey && $request->input('api_key') !== $apiKey) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            // Validate Request
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string',
                'image_url' => 'nullable|url',
                'status' => 'nullable|boolean',
            ]);

            $imagePath = null;
            if (!empty($validated['image_url'])) {
                try {
                    $imageContent = Http::get($validated['image_url'])->body();
                    $imageName = 'blog/' . Str::slug($validated['title']) . '-' . time() . '.jpg';
                    Storage::disk('public')->put($imageName, $imageContent);
                    $imagePath = $imageName;
                } catch (\Exception $e) {
                    Log::error("N8N Blog Image Download Error: " . $e->getMessage());
                }
            }

            $post = BlogPost::create([
                'title' => $validated['title'],
                'slug' => Str::slug($validated['title']),
                'content' => $validated['content'],
                'excerpt' => $validated['excerpt'] ?? null,
                'meta_title' => $validated['meta_title'] ?? null,
                'meta_description' => $validated['meta_description'] ?? null,
                'image_path' => $imagePath,
                'status' => $validated['status'] ?? true, // Default to published
                'published_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Blog post published successfully!',
                'data' => [
                    'id' => $post->id,
                    'url' => url('/blog/' . $post->slug)
                ]
            ], 201);
        } catch (\Exception $e) {
            Log::error("N8N Webhook Error: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
