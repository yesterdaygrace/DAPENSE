<?php

// app/Http/Controllers/ProductController.php

namespace App\Http\Controllers\rootsuperuser;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProductControllerRootSuperuser
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        $total = User::count();

        return view('rootsuperuser.product.home', compact(['users', 'total']));
    }

    public function create()
    {
        return view('rootsuperuser.product.create');
    }

    public function save(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'usertype' => 'required|string|in:user,rootsuperuser,admin,operator,bod',
            'password' => 'required|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $imagePath = $request->file('image') ? $request->file('image')->store('images', 'public') : null;

        $data = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'usertype' => $request->usertype,
            'password' => Hash::make($request->password),
            'image' => $imagePath,
        ]);

        event(new Registered($data));

        if ($data) {
            session()->flash('success', 'User Added Successfully');

            return redirect()->route('rootsuperuser/products');
        } else {
            session()->flash('error', 'Some problem occurred');

            return redirect()->route('rootsuperuser/products/create');
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        return view('rootsuperuser.product.update', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'usertype' => 'required|string|in:user,admin,operator,bod',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            // Upload new image
            $user->image = $request->file('image')->store('images', 'public');
        }

        $user->name = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $request->validate([
                'password' => 'required|min:8',
            ]);
            $user->password = Hash::make($request->input('password'));
        }

        $user->usertype = $request->usertype;

        if ($user->save()) {
            session()->flash('success', 'User Updated Successfully');

            return redirect()->route('rootsuperuser/products');
        } else {
            session()->flash('error', 'Some problem occurred');

            return redirect()->route('rootsuperuser/products/edit', $id);
        }
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        if ($user->delete()) {
            // Delete image if exists
            if ($user->image) {
                Storage::disk('public')->delete($user->image);
            }
            session()->flash('success', 'User Deleted Successfully');

            return redirect()->route('rootsuperuser/products');
        } else {
            session()->flash('error', 'Some problem occurred');

            return redirect()->route('rootsuperuser/products');
        }
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = ! $user->status;
        $user->save();

        session()->flash('success', 'User status updated successfully');

        return redirect()->route('rootsuperuser/products');
    }
}
