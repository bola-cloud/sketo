<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class RoleUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        return view('admin.role_user.index', compact('users', 'roles'));
    }

    public function attachRole(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $user = User::findOrFail($validatedData['user_id']);
        $user->roles()->attach($validatedData['role_id'], ['user_type' => get_class($user)]);
    
        return redirect()->back()->with('success', 'تم اضافة الدور بنجاح');
    }
    

    public function detachRole(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);
    
        $user = User::findOrFail($validatedData['user_id']);
        $user->roles()->wherePivot('user_type', get_class($user))->detach($validatedData['role_id']);
    
        return redirect()->back()->with('success', 'تم ازالة الدور بنجاح');
    }
    
}
