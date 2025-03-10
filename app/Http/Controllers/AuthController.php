<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        return response()->json([
            'message' => 'Unauthorized',
        ],  401);
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out sucessfully',
        ]);
    }



    public function register(UserRequest $request)
    {
        $credentials = $request->validated();

        $user = User::create($credentials);
        if ($user->role === 'STDNT') {
            $student = $user->student()->created([
                'class_id' => $credentials['class_id']
            ]);

            return response()->json([
                'user' => $user,
                'student' => $student,
                'message' => 'Student created successfully'
            ]);
        } else {
            $teacher = $user->teacher()->created([
                'subject_name' => $credentials['subject_name']
            ]);

            return response()->json([
                'user' => $user,
                'teacher' => $teacher,
                'message' => 'Teacher created successfully'
            ]);
        }

        return response()->json([
            'message' => 'Could not create user',
        ],  400);
    }



    public function me(Request $request)
    {
        return response()->json(['user' => $request->user()]);
    }
}
