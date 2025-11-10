<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        // Validate the request data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt to authenticate the user
        if (auth()->attempt($request->only('email', 'password'))) {



            $user = auth()->user();
            $user->tokens()->delete();
            $token = $user->createToken('auth_token')->plainTextToken;
            $user->load('role');

            $userModel = new User();
            activity('auth_log')
                ->performedOn($userModel)
                ->event('login')
                ->causedBy($user)
                ->log("{$user->name} has Logged In");

            return response()->json([
                'access_token' => $token,
                'message' => 'Hello ' . $user->name . ', welcome back!',
                'token_type' => 'Bearer',
                'user' => UserResource::make($user),
            ]);
        }

        return response()->json(['message' => 'Invalid login details'], 401);
    }

    public function logout()
    {
        $user = auth()->user();
        $user->tokens()->delete();


        $userModel = new User();
        activity('auth_log')
            ->performedOn($userModel)
            ->event('logout')
            ->causedBy($user)
            ->log("{$user->name} has Logged Out");

        return response()->json(['message' => 'Successfully logged out']);
    }
}
