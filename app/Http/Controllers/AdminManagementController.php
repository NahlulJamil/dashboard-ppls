<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = Admin::where('role', 'admin')->get();
        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:admins,username',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[!?@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/`~]/',
            ],
        ], [
            'password.min'   => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar dan karakter spesial (seperti !?@#$% dll).',
        ]);

        Admin::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'admin',
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin berhasil ditambahkan!');
    }

    public function edit(Admin $admin)
    {
        if ($admin->role === 'super_admin') {
            abort(403);
        }
        return view('admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        if ($admin->role === 'super_admin') {
            abort(403);
        }

        $request->validate([
            'username' => ['required', 'string', Rule::unique('admins')->ignore($admin->id_admin, 'id_admin')],
            'password' => [
                'nullable',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[!?@#$%^&*()_+\-=\[\]{};:\'"\\|,.<>\/`~]/',
            ],
        ], [
            'password.min'   => 'Password minimal 8 karakter.',
            'password.regex' => 'Password harus mengandung huruf besar dan karakter spesial (seperti !?@#$% dll).',
        ]);

        $admin->username = $request->username;
        if ($request->filled('password')) {
            $admin->password = Hash::make($request->password);
        }
        $admin->save();

        return redirect()->route('admins.index')->with('success', 'Data admin berhasil diperbarui!');
    }

    public function destroy(Admin $admin)
    {
        if ($admin->role === 'super_admin') {
            abort(403);
        }

        $admin->delete();

        return redirect()->route('admins.index')->with('success', 'Admin berhasil dihapus!');
    }
}
