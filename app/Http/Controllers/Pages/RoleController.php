<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $role = Role::when($search, function($query, $search) {
                    return $query->where('name', 'like', "%{$search}%");
                })
                ->orderBy('id','desc')
                ->paginate($sort)
                ->appends($request->query());

        return view("pages.role.index", compact("role"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("pages.role.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string"
        ]);

        $post = $request->all();

        Role::create($post);

        return redirect()->route('role.index')->with('success', 'Berhasil menambahkan jabatan baru.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);

        return view("pages.role.edit", compact("role"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $role = Role::find($id);

        $request->validate([
            "name" => "required|string"
        ]);

        $put = $request->all();

        $role->update($put);

        return redirect()->route('role.index')->with('success', 'Berhasil mengubah jabatan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);

        if (!$role) {
            return back()->with('error', 'Gagal Mengapus Jabatan.');
        }

        $role->delete();

        return back()->with('success', 'Berhasil menghapus jabatan.');
    }
}
