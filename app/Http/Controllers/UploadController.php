<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function upload(Request $request)
    {
        
        // if ($request->hasFile('upload')) {
        //     $file = $request->file('upload');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $path = $file->storeAs('uploads', $filename, 'public');

        //     return response()->json([
        //         'url' => asset('storage/' . $path)
        //     ]);
        // }

        // return response()->json(['error' => 'File upload failed.'], 400);


        \Log::info("File upload request received");

        if ($request->hasFile('upload')) {
            $file = $request->file('upload');

            // Log file details for debugging
            \Log::info("Uploaded file details:", [
                'name' => $file->getClientOriginalName(),
                'type' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);

            // Validate
            $request->validate([
                'upload' => 'required|mimes:jpg,jpeg,png,gif,doc,docx,pdf,zip,txt,ppt,pptx|max:10240'
            ]);

            // Save file
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads', $filename, 'public');

            \Log::info("File uploaded successfully at path: " . $path);

            return response()->json([
                'url' => asset('storage/' . $path)
            ]);
        }

        \Log::error("No file received for upload.");
        return response()->json(['error' => 'File upload failed.'], 400);

        }
}