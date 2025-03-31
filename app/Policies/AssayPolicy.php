<?php

namespace App\Policies;

use App\Models\Assay;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssayPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    // public function viewAny(User $user): bool
    // {
    //     return false;
    // }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assay $assay): Response
    {
        if ($user->role === 'TCHR') {

            $logged_teacher = Teacher::where('user_id', $user->id)->first();
            $teacher_owner = $logged_teacher->id == $assay->teacher_id;
            
            dd($logged_teacher, $teacher_owner);
            if (! ($logged_teacher && $teacher_owner)) {

                return Response::deny('You cannot acess this assay for now on', 401);
            }
    
        }
        else if ($user->role === 'STDNT') {
            
            $logged_student = Student::where('user_id', $user->id)->first();
            if ($logged_student->class_tag_id != $assay->class_tag_id) {

                return Response::deny('You cannot acess this assay for now on', 401);
            }
        }
        

        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        $logged_teacher = Teacher::where('user_id', $user->id)->first();
        $teacher_owner = $logged_teacher->id == $assay->teacher_id;
        
        dd($logged_teacher, $teacher_owner);
        if (! ($logged_teacher && $teacher_owner)) {

            return Response::deny('You cannot acess this assay for now on', 401);
        }
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assay $assay): Response
    {
        $is_teacher = $user->role == 'TCHR';
        
        $logged_teacher = Teacher::where('user_id', $user->id)->first();
        $teacher_owner = $logged_teacher->id == $assay->teacher_id;
        if ($is_teacher && $teacher_owner) {
            return Response::allow();
        }
        return Response::deny('You cannot update this assay', 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assay $assay): Response
    {
        $is_teacher = $user->role == 'TCHR';
        
        $logged_teacher = Teacher::where('user_id', $user->id)->first();
        $teacher_owner = $logged_teacher->id == $assay->teacher_id;
        if ($is_teacher && $teacher_owner) {
            return Response::allow();
        }
        return Response::deny('You cannot delete this assay', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assay $assay): Response
    {
        $is_teacher = $user->role == 'TCHR';
        
        $logged_teacher = Teacher::where('user_id', $user->id)->first();
        $teacher_owner = $logged_teacher->id == $assay->teacher_id;
        if ($is_teacher && $teacher_owner) {
            return Response::allow();
        }
        return Response::deny('You cannot restore this assay', 403);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assay $assay): Response
    {
        $is_teacher = $user->role == 'TCHR';
        
        $logged_teacher = Teacher::where('user_id', $user->id)->first();
        $teacher_owner = $logged_teacher->id == $assay->teacher_id;
        if ($is_teacher && $teacher_owner) {
            return Response::allow();
        }
        return Response::deny('You cannot delete this assay', 403);
    }
}
