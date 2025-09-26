<?php

namespace App\Http\Controllers;

use Doctrine\Inflector\Rules\Word;
use Faker\Provider\ar_EG\Text;
use Illuminate\Http\Request;
use App\Models\Chirp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ChirpController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $chirps = Chirp::with('user')->latest()->get();
    
        return view('home', ['chirps' => $chirps]);
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);

        if (!Auth::check()) {
            Chirp::create($validated);

            return redirect('/')->with('success', 'Chirp created!');
        }
    
        // Create the chirp (no user for now - we'll add auth later)
        auth()->user()->chirps()->create($validated);
    
        // Redirect back to the feed
        return redirect('/')->with('success', 'Chirp created!');
    }

    public function edit(Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        return view('chirps.edit', compact('chirp'));
    }

    public function update(Request $request, Chirp $chirp)
    {
        $this->authorize('update', $chirp);

        // Validate
        $validated = $request->validate([
            'message' => 'required|string|max:255',
        ]);
    
        // Update
        $chirp->update($validated);
    
        return redirect('/')->with('success', 'Chirp updated!');
    }

    public function destroy(Chirp $chirp)
    {
        $this->authorize('delete', $chirp);

        $chirp->delete();
    
        return redirect('/')->with('success', 'Chirp deleted!');
    }
}
