<?php

namespace App\Policies;

use App\Models\Assay;
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
        if($user->role == 'TCHR') {
            return Response::allow();
        }
        if ($assay->is_visible == true) {
            return Response::allow();
        }

        return Response::deny('You cannot acess this assay for now on', 401);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if ($user->role == 'TCHR') {
            Response::allow();
        }
        return Response::deny('You cannot create an assay', 403);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Assay $assay): Response
    {
        if ($user->role == 'TCHR') {
            return Response::allow();
        }
        return Response::deny('You cannot create an assay', 403);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Assay $assay): Response
    {
        if ($user->role == 'TCHR') {
            return Response::allow();
        }
        return Response::deny('You cannot create an assay', 403);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Assay $assay): Response
    {
        if ($user->role == 'TCHR') {
            return Response::allow();
        }
        return Response::deny('You cannot create an assay', 403);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Assay $assay): Response
    {
        if ($user->role == 'TCHR') {
            return Response::allow();
        }
        return Response::deny('You cannot create an assay', 403);
    }
}
