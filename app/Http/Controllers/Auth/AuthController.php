<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as RulesPassword;
use \Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
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
            'role' => $user->role,
            'message' => 'User ' . $user->name . ' Created Succesfully',
        ]);
    }

    public function login(Request $request): JsonResponse
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
        $user['remember_me'] = $request->remember_me;
        return response()->json([
            'status' => true,
            'message' => 'Login Successfull',
            'access_token' => $token,
            'role' => $user->role,
            'user_details' => $user,
        ]);
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => 'logged out successfully'
        ]);
    }

    public function user(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        return response()->json([
            'status' => true,
            'message' => 'User Data',
            'data' => $user
        ]);
    }

    public function updateUser(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        $updateUserData = $request->validate([
            'name' => 'required|string',
            'username' => 'required|string|unique:users,username,' . $user->id,
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'phone_number' => ['required', 'min:10', 'max:11'],
        ]);
        $user->updateOrFail($updateUserData);
        return response()->json([
            'status' => true,
            'message' => 'User ' . $user->username . ' Updated Succesfully',
            'user_details' => $user,
        ]);
    }

    public function updateProfileImage(Request $request): JsonResponse
    {
        $user = User::find($request->id);
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Check if user already has a profile image
        if ($user->profile_image) {
            $existingImagePath = public_path('uploads/profiles/' . $user->profile_image);
            if (file_exists($existingImagePath)) {
                unlink($existingImagePath);
            }
        }

        $profile_image = $request->file('profile_image');
        $profile_image_name = time() . '.' . $profile_image->extension();
        $profile_image->move(public_path('uploads/profiles'), $profile_image_name);
        $user->profile_image = $profile_image_name;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => $user->username . ' Profile Image Updated Successfully',
            'profile_img_url' => url('uploads/profiles/' . $profile_image_name),
        ]);
    }
}
