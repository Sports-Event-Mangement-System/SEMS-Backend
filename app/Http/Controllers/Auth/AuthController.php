<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as RulesPassword;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $registerUserData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'phone_number' => ['required', 'max:10'],
            'password' => [
                'required',
                'confirmed',
                RulesPassword::min(8)->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
            'password_confirmation' => 'required|same:password',
        ]);
        $user = User::create([
            'name' => $registerUserData['name'],
            'username' => $registerUserData['username'],
            'email' => $registerUserData['email'],
            'phone_number' => $registerUserData['phone_number'],
            'password' => Hash::make($registerUserData['password']),
            'role' => 'user'
        ]);
        return response()->json([
            'status' => true,
            'message' => 'User ' . $user->name . ' Created Succesfully',
        ]);
    }
    public function login(Request $request)
    {
        $loginUserData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|min:8'
        ]);
        $user = User::where('email', $loginUserData['email'])->first();
        if (!$user || !Hash::check($loginUserData['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;
        return response()->json([
            'status' => true,
            'message' => 'Login Successfull',
            'access_token' => $token,
        ]);
    }

    public function logout()
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'logged out successfully'
        ]);
    }

    public function user(Request $request){
        return response()->json([
            'status' => true,
            'message' => 'User Data',
            'data' => auth()->user()
        ]);
    }
}
