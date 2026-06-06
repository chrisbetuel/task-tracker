<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdatePasswordRequest;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function show()
    {
        $user = request()->user();
        return view('profile.index', compact('user'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = request()->user();

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('profile.show')
            ->with('success', 'Password changed successfully.');
    }
}
