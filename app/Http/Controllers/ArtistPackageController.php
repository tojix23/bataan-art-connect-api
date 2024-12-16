<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtistPackage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ArtistPackageController extends Controller
{
    public function add_package(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'acc_id' => 'required',
            'package_name' => 'required',
            'amount' => 'required',
            'package_desc' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }
        // Use a database transaction to create the record


        try {
            DB::beginTransaction();

            $packagefilepath = null;
            // Handle certificate file upload
            if ($request->hasFile('package_image')) {
                $package_file = $request->file('package_image');
                $packagefilepath = $package_file->store('package_image', 'public');
            }

            // Save file path to certificates table
            $artistPackage = ArtistPackage::create([
                'acc_id' => $request->acc_id,
                'package_name' => $request->package_name,
                'package_desc' => $request->package_desc,
                'amount' => $request->amount,
                'is_active' => true,
                'image_attach' => $packagefilepath ?? "N/A", // Use null coalescing operator
            ]);

            DB::commit(); // Commit the transaction if successful

            return response()->json([
                'message' => 'Artist package created successfully!',
                'data' => $artistPackage
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction if there's an error

            return response()->json([
                'message' => 'Failed to create artist package!',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function package_list(Request $request)
    {
        // Find the user by email
        $artistPackage = ArtistPackage::where('acc_id', $request->acc_id)->get();

        // If the user doesn't exist, return an error
        if (!$artistPackage) {
            return response()->json(['error' => 'ArtistPackage not found.'], 404);
        }


        return response()->json([
            'message' => 'ArtistPackage artists',
            'data' => $artistPackage,
            'status' => 1,
        ]);
    }

    public function package_list_enabled(Request $request)
    {
        // Find the user by email
        $artistPackage = ArtistPackage::where('acc_id', $request->acc_id)->where('is_active', true)->get();

        // If the user doesn't exist, return an error
        if (!$artistPackage) {
            return response()->json(['error' => 'ArtistPackage not found.'], 404);
        }


        return response()->json([
            'message' => 'ArtistPackage artists',
            'data' => $artistPackage,
            'status' => 1,
        ]);
    }

    public function enable_disable_package(Request $request)
    {

        // Find the artist package
        $artistPackage = ArtistPackage::find($request->package_id);

        if (!$artistPackage) {
            return response()->json([
                'message' => 'Artist package not found or deleted!',
            ], 404);
        }
        // Update the artist package
        $artistPackage->is_active = !$request->status;
        $artistPackage->save();

        return response()->json([
            'message' => 'Artist package updated successfully!',
            'data' => $artistPackage
        ], 200);
    }
}
