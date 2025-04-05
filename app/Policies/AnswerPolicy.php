<?php

namespace App\Policies;

use App\Models\Answer;
use App\Models\Assay;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AnswerPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Answer $answer): Response
    {
        $assay = Assay::find($answer->assay_id);
        if ($user->role === 'TCHR') {

            $teacher = User::where('user_id', $user->id);
            $allowed = $teacher->id === $assay->teacher_id;
        }

        if ($user->role === 'STDNT') {

            $student = User::where('user_id', $user->id);
            $allowed = $student->id === $answer->student_id;
        }

        if ($allowed) {

            return Response::allow();
        }

        return Response::deny('You cannot acess this answer');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role == 'STDNT';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Answer $answer): bool
    {
        $assay = Assay::find($answer->assay_id);
        return $user->role === 'TCHR' && $assay->teacher_id === $user->teacher->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Answer $answer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Answer $answer): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Answer $answer): bool
    {
        return false;
    }
}
