<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherToClass;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function getUsers()
    {
        return response()->json([
            'users' => User::all(),
        ]);
    }


    public function getUser(string $id)
    {
        return response()->json([
            'user' => User::find($id),
        ]);
    }


    public function rewriteUser(UserRequest $request, string $id)
    {   
        $userinfo = $request->validated();
        $user = User::find($id);
        $user->update($userinfo);

        $teacher = Teacher::where('user_id', $user->id)->first();
        $teacher->update(['subject_id' => $userinfo['subject_id']]);

        TeacherToClass::where('teacher_id', $teacher->id)->delete();
        $classes = array_map(fn ($class_id) => TeacherToClass::create([
            'teacher_id' => $teacher->id,
            'class_tag_id' => $class_id,
        ]),
            $userinfo['classes_ids']
        );
        
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



    public function register(UserRequest $request)
    {
        $credentials = $request->validated();

        $user = User::create($credentials);
        if ($user->role == 'STDNT') {
            $student = Student::create([
                'user_id' => $user->id,
                'class_tag_id' => $credentials['class_tag_id']
            ]);

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

        if ($user->role == 'TCHR') {
            $teacher = Teacher::create([
                'user_id' => $user->id,
                'subject_id' => $credentials['subject_id']
            ]);

            array_map(fn ($class_id) => TeacherToClass::create([
                'teacher_id' => $teacher->id,
                'class_tag_id' => $class_id,
            ]),
                $credentials['classes_ids']
            );
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

        return response()->json([
            'user' => $user,
        ]);
    }



    public function eraseUser(string $id)
    {
        $user = User::find($id);
        $user->delete();
        return response()->json([
            'message' => 'User deleted',
        ], 200);
    }
}
