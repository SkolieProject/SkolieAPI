<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\UserRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherToClass;
use App\Models\User;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials) == false) {
            
            return response()->json([
                'message' => 'Unauthorized',
            ],  401);
        }
        
        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;
        
        return response()->json([
            'token' => $token,
        ]);
            
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out sucessfully',
        ]);
    }







    public function me(Request $request)
    {
        $user = Auth::user();
        if ($user->role == 'TCHR') {
            $teacher = Teacher::with('subject')->where('user_id', $user->id)->first();
            
            $classes = TeacherToClass::with('class_tag')->where('teacher_id', $teacher->id)->get();
            $classes = $classes->map(fn ($class) => $class->class_tag);
            
            $teacherinfo = [
                'id' => $teacher->id,
                'subject' => $teacher->subject,
                'classes' => $classes
            ];
            return response()->json([
                'user' => $user,
                'teacher' => $teacherinfo,
            ]);
        }
        if ($user->role == 'STDNT') {
            $student = Student::with('class_tag')->where('user_id', $user->id)->first();
            
            $studentinfo = [
                'id' => $student->id,
                'class' => $student->class_tag
            ];
            return response()->json([
                'user' => $user,
                'student' => $studentinfo,
            ]);
        }

        return response()->json([
            'user' => $user,
        ]);
      
    }
}
