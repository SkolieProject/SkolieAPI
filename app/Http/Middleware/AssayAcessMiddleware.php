<?php

namespace App\Http\Middleware;

use App\Models\Teacher;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AssayAcessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $id = $request->user()->id;
        $teacher = Teacher::where('user_id', $id)->first();
        

        if ($request->subject_id != $teacher->subject_id) {
            return response()->json([
                'message' => 'Você não é professor dessa matéria'
            ], 404);
        }

        return $next($request);
    }
}
