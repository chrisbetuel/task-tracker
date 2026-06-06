<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller

{
    protected $redirectTo = '/';


    public function showRegistrationForm()
    {
        $departments = Department::orderBy('name')->get();
        return view('auth.register', compact('departments'));
    }

    public function register(Request $request)
    {
        $validated = $this->validator($request->all())->validate();

        $user = $this->create($validated);

        Auth::login($user);

        return redirect()->intended($this->redirectTo);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'department_id' => ['required', 'string', 'exists:departments,id'],
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'department_id' => $data['department_id'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'team_member',
        ]);
    }
}
