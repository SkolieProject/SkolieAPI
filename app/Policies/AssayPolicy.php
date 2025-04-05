<?php

namespace App\Policies;

use App\Models\Assay;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherToClass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\Response;

class AssayPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        // TODO: Resolver as regras de negócio disso aqui
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Assay $assay): Response
    {
        if ($user->role === 'TCHR') {
            
            $teacher = Teacher::where('user_id', $user->id)->first();
            
            return $teacher->id === $assay->teacher_id
                ? Response::allow()
                : Response::deny('You cannot access this assay');
        }

        if ($user->role === 'STDNT') {
            
            $student = Student::where('user_id', $user->id)->first();

            $is_from_class = $student->class_tag_id === $assay->class_tag_id;
            
            return $is_from_class && $assay->is_answerable
                ? Response::allow()
                : Response::deny('You cannot access this assay');
        }

        return Response::deny('Unauthorized role');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if ($user->role !== 'TCHR') {
            
            return Response::deny('Only teachers can create assays');
        }

        $teacher = Teacher::where('user_id', $user->id)->first();
        if ($teacher->subject_id !== request()->json('subject_id')) {

            return Response::deny('You are not allowed to create this assay');
        }

        if (! $class_tag_id = request()->json('class_tag_id')) {
        
            return Response::allow();
        }
        $classes = TeacherToClass::where('teacher_id', $teacher->id)->get();

        if (! in_array($class_tag_id, $classes->toArray())) {
        
            return Response::deny('You are not allowed to associate this assay with this class');
        }

        return Response::allow();

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assay $assay): Response
    {
        if ($user->role !== 'TCHR') {
        
            return Response::deny('You cannot update this assay', 403);
        }   

        $teacher = Teacher::where('user_id', $user->id)->first();

        if ($teacher->id !== $assay->teacher_id) {

            return Response::deny('You cannot update this assay', 403);
        }

        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): Response
    {
        if ($user->role !== 'TCHR') {
        
            return Response::deny('You cannot delete this assay', 403);
        }   

        $teacher = Teacher::where('user_id', $user->id)->first();
        
        $assay = Assay::find(request()->id());
        
        if (! $assay) {
            
            return Response::deny('Assay not found', 404);
        }

        if ($teacher->id !== $assay->teacher_id) {

            return Response::deny('You cannot delete this assay', 403);
        }

        return Response::allow(); 
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assay $assay): Response
    {
        if ($user->role !== 'TCHR') {
        
            return Response::deny('You cannot delete this assay', 403);
        }   

        $teacher = Teacher::where('user_id', $user->id)->first();
        $assay = Assay::find(request()->id);
        
        if (! $assay) {
            
            return Response::deny('Assay not found', 404);
        }

        if ($teacher->id !== $assay->teacher_id) {

            return Response::deny('You cannot delete this assay', 403);
        }

        return Response::allow(); 

    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assay $assay): Response
    {
        if ($user->role !== 'TCHR') {
        
            return Response::deny('You cannot delete this assay', 403);
        }   

        $teacher = Teacher::where('user_id', $user->id)->first();
        $assay = Assay::find(request()->id);
        
        if (! $assay) {
            
            return Response::deny('Assay not found', 404);
        }

        if ($teacher->id !== $assay->teacher_id) {

            return Response::deny('You cannot delete this assay', 403);
        }

        return Response::allow(); 

    }
}
