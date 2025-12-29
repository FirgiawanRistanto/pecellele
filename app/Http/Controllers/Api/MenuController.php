<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage; // Added this import

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Menu::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For file upload
            'price' => 'required|integer|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        $imageName = null;
        if ($request->hasFile('image_file')) {
            $imageName = time() . '.' . $request->image_file->extension();
            $request->image_file->move(public_path('images'), $imageName);
        } else {
            // If no file is uploaded, ensure there's a default or it's handled as nullable
            // For now, let's make sure it's not trying to save a non-existent image
            $imageName = 'default.jpg'; // You might want a default image or make this field nullable
        }

        $validatedData['image'] = $imageName; // Store the filename in the database

        $menu = Menu::create($validatedData);

        return response()->json($menu, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Menu $menu)
    {
        return $menu;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Menu $menu)
    {
        $validatedData = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // For file upload
            'image' => 'sometimes|string|max:255', // Existing image name if no new file is uploaded
            'price' => 'sometimes|required|integer|min:0',
            'stock' => 'sometimes|required|integer|min:0',
        ]);

        if ($request->hasFile('image_file')) {
            // Delete old image if it's not a default one
            if ($menu->image && $menu->image !== 'default.jpg' && file_exists(public_path('images/' . $menu->image))) {
                unlink(public_path('images/' . $menu->image));
            }

            $imageName = time() . '.' . $request->image_file->extension();
            $request->image_file->move(public_path('images'), $imageName);
            $validatedData['image'] = $imageName;
        } else {
            // If no new file, keep the existing image name (sent via 'image' hidden field)
            $validatedData['image'] = $request->input('image', $menu->image);
        }
        
        // Remove image_file from validatedData before updating menu model
        unset($validatedData['image_file']);

        $menu->update($validatedData);

        return response()->json($menu);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Menu $menu)
    {
        // Delete associated image file if it exists and is not a default one
        if ($menu->image && $menu->image !== 'default.jpg' && file_exists(public_path('images/' . $menu->image))) {
            unlink(public_path('images/' . $menu->image));
        }
        
        $menu->delete();

        return response()->noContent();
    }
}
