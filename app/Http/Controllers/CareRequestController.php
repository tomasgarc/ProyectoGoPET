<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CareRequest;

class CareRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careRequests = auth()->user()->careRequests()->with('dogs')->latest()->get();
        return view('care_requests.index', compact('careRequests'));
    }

    /**
     * Display a listing of care requests from other users.
     */
    public function explore()
    {
        $careRequests = CareRequest::where('user_id', '!=', auth()->id())
            ->where('status', 'pending')
            ->with(['dogs', 'user'])
            ->latest()
            ->get();

        return view('care_requests.explore', compact('careRequests'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $dogs = auth()->user()->dogs;
        return view('care_requests.create', compact('dogs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dogs' => 'required|array',
            'dogs.*' => 'exists:dogs,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $careRequest = auth()->user()->careRequests()->create([
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'price' => $request->price,
            'description' => $request->description,
            'status' => 'pending',
        ]);

        $careRequest->dogs()->attach($request->dogs);

        return redirect()->route('care-requests.index')->with('success', 'Petición de cuidado creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CareRequest $careRequest)
    {
        $careRequest->load('dogs');
        return view('care_requests.show', compact('careRequest'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CareRequest $careRequest)
    {
        // Ensure the user owns this request
        if ($careRequest->user_id !== auth()->id()) {
            abort(403);
        }

        $careRequest->delete();

        return redirect()->route('care-requests.index')->with('success', 'Petición eliminada correctamente.');
    }
}
