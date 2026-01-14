<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManageUserController extends Controller
{
    public function manageUser(Request $request)
    {
        $totalUser = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalCashier = User::where('role', 'cashier')->count();

        $filter = $request->get('filter', 'all');

        if ($filter === 'admin') {
            $users = User::where('role', 'admin')->get();
        } elseif ($filter === 'cashier') {
            $users = User::where('role', 'cashier')->get();
        } else {
            $users = User::all();
        }

        return view('admin.manageUser', compact('users', 'totalUser', 'totalAdmin', 'totalCashier', 'filter'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        return view('admin.manageUser', compact('user'));
    }

    public function destroyUser($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.manageUser')->with('success', 'User berhasil dihapus');
    }
    public function storeUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|in:admin,cashier',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        return redirect()->route('admin.manageUser')->with('success', 'User berhasil ditambahkan!');
    }
    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,cashier',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->role = $request->role;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('admin.manageUser')->with('success', 'User berhasil diperbarui!');
    }
}
