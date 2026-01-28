<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Train;

class TrainsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Train::with(['rangkaians' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])->withCount('rangkaians');

        if ($request->has('search') && $request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $sort = $request->get('sort', 10);
        $trains = $query->paginate($sort);

        return view('pages.kereta.index', compact('trains'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pages.kereta.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Train::create([
            'name' => $request->name,
        ]);

        return redirect()->route('train.index')->with('success', 'Data kereta berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $train = Train::with(['rangkaians' => function($q) {
            $q->orderBy('urutan', 'asc');
        }])->findOrFail($id);
        
        return view('pages.kereta.edit', compact('train'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $train = Train::findOrFail($id);
        $train->update([
            'name' => $request->name,
        ]);

        return redirect()->route('train.index')->with('success', 'Data kereta berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $train = Train::findOrFail($id);
        $train->delete();

        return redirect()->route('train.index')->with('success', 'Data kereta berhasil dihapus');
    }
}
