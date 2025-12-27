<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    /**
     * Upload an image file
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            // Store in public storage
            $path = $file->storeAs('uploads/images', $filename, 'public');
            
            // Get the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ],
                'message' => 'Image uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload a file (general purpose)
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $filename = Str::uuid() . '.' . $extension;
            
            // Store in public storage
            $path = $file->storeAs('uploads/files', $filename, 'public');
            
            // Get the public URL
            $url = Storage::url($path);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'url' => $url,
                    'path' => $path,
                    'filename' => $filename,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ],
                'message' => 'File uploaded successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }
}
