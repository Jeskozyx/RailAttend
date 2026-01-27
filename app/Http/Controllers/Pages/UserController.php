<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = $request->sort ?? 10;
        $search = $request->search ?? null;

        $user = User::when($search, function($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                })
                ->orderBy('id','desc')
                ->paginate($sort)
                ->appends($request->query());

        return view("pages.user.index", compact("user"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $role = Role::all();
        return view("pages.user.create", compact("role"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string",
            "email" => "required|string|unique:users,email",
            "role" => "required",
            "password" => "required|confirmed|min:8",
            "password_confirmation" => "required|min:8"
        ]);

        $post = $request->except('role', 'password_confirmation');

        $user = User::create($post);

        $user->assignRole($request->role);

        return redirect()->route('user.index')->with('success', 'Berhasil menambahkan user baru.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::all();

        $user = User::find($id);

        return view("pages.user.edit", compact("role", "user"));
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $request->validate([
            "name" => "required|string",
            'email' => [
                'required',
                'string',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            "role" => "required",
            "password" => "nullable|confirmed|min:8",
        ]);

        $data = $request->except(['role', 'password_confirmation']);

        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        $user->syncRoles([$request->role]);

        return redirect()->route('user.index')->with('success', 'Berhasil mengupdate user.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);

        if(!$user) {
            return back()->with('error', 'Gagal Menghapus data.');
        }

        $user->delete();

        return back()->with('success', 'Berhasil menghapus user.');
    }
}
