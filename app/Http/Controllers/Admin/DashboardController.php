<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function manageUser()
    {
        $totalUser = User::count();
        $totalAdmin = User::where('role', 'admin')->count();
        $totalCashier = User::where('role', 'cashier')->count();

        $users = User::all(); // atau sesuaikan dengan kebutuhan

        return view('admin.manageUser', compact('users', 'totalUser', 'totalAdmin', 'totalCashier'));
    }
    // public function manageProduct()
    // {
    //     return view('admin.manageProduct');
    // }
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
