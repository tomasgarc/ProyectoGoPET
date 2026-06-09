<?php

namespace App\Http\Controllers;

use App\Models\Dog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $dogs = auth()->user()->dogs;

        return view('dogs.index', compact('dogs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dogs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'size' => 'required|string|max:255',
            'sex' => 'required|string|in:macho,hembra',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $data['user_id'] = auth()->id();
        if (isset($data['size'])) {
            $data['size'] = strtolower(trim($data['size']));
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('dogs', 'public');
            $data['photo'] = $path;
        }

        Dog::create($data);

        return redirect()->route('dogs.index')->with('success', 'Perro añadido correctamente.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $dog = auth()->user()->dogs()->findOrFail($id);

        return view('dogs.edit', compact('dog'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $dog = auth()->user()->dogs()->findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'breed' => 'required|string|max:255',
            'age' => 'nullable|integer',
            'size' => 'required|string|max:255',
            'sex' => 'required|string|in:macho,hembra',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        if (isset($data['size'])) {
            $data['size'] = strtolower(trim($data['size']));
        }

        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($dog->photo) {
                Storage::disk('public')->delete($dog->photo);
            }
            $path = $request->file('photo')->store('dogs', 'public');
            $data['photo'] = $path;
        }

        $dog->update($data);

        return redirect()->route('dogs.index')->with('success', 'Perro actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $dog = auth()->user()->dogs()->findOrFail($id);

        if ($dog->photo) {
            Storage::disk('public')->delete($dog->photo);
        }

        $dog->delete();

        return redirect()->route('dogs.index')->with('success', 'Perro eliminado correctamente.');
    }
}
