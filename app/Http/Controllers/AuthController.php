<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\{Hash, Auth};

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $passwordRule = Password::min(6)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();
        // ->uncompromised()

        $request->validate([
            'name' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', $passwordRule]
        ]);

        $passwordHash = Hash::make($request->get('password'));

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $passwordHash
        ]);

        return response()->json(status: 201, data: [
            'user' => $user
        ]);
    }

    public function login(Request $request)
    {
        $passwordRule = Password::min(6)
            ->letters()
            ->mixedCase()
            ->numbers()
            ->symbols();

        $request->validate([
            'email' => 'required|string|email|max:255',
            'password' => ['required', 'string', $passwordRule]
        ]);

        $loginSuccessful = Auth::attempt($request->only('email', 'password'));

        if (!$loginSuccessful) {
            $error = [
                'errors' => [
                    'root' => ['We were unable to sign you in with those details.']
                ]
            ];

            return response()->json(status: 422, data: $error);
        }

        $request->session()->regenerate();
        return response()->json(status: 200, data: Auth::user());
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(status: 204);
    }

    public function user(Request $request)
    {
        return $request->user()->load('tasks');
    }
}
