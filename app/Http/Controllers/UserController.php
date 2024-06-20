<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
Use Alert;


class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        $users = User::latest()->paginate(10);

        $title = 'Delete User!';
        $text = "Are you sure you want to delete?";
        confirmDelete($title, $text);
      
        return view('welcome', compact('users'))->with('success');
    }

    public function store(Request $request)
    {
        $user = User::updateOrCreate(['id' => $request->id], $request->all());
        return response()->json($user);
    }

    public function edit($id)
    {
        $user = User::find($id);
        return response()->json($user,);
    }

    public function update(Request $request, $id)
    {
        $user = User::find($id);
        $user->update($request->all());
        return response()->json($user);
    }

    public function destroy(Request $request)
    {
        // dd($request->id);
        $user = User::findOrFail($request->id);
        $user->delete();

        return response()->json(['message' => 'Item deleted successfully']);
    }
}
