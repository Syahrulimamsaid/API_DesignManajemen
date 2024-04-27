<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\JobAssignment;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class DesignerJob
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $designer = Auth::user()->id;
        $job = JobAssignment::where('job_id',$request->id)->firstOrFail();

        if ($job->designer_id != $designer->id) {
            return response()->json(['message' => 'data not found'], 404);
        }
        return $next($request);
    }
}
