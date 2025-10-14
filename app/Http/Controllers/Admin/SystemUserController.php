<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;

class SystemUserController extends Controller
{
    /**
     * Display a list of all users.
     */
    public function index()
    {
        $users = User::all(); // fetch all users
        return view('admin.system-users', compact('users'));
    }

    /**
     * Return a single user as JSON for editing.
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * Update an existing user.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => "required|email|unique:users,email,{$user->id}",
            'role'  => 'required|in:admin,billing_clerk,receivable_clerk',
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
        ]);

        return redirect()->back()->with('success', 'User updated successfully!');
    }

    /**
     * Delete a user.
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully!');
    }
}
