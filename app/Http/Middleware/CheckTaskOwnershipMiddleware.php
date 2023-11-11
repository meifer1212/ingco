<?php

namespace App\Http\Middleware;

use App\Models\Task;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTaskOwnershipMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */

    public function handle($request, Closure $next): Response
    {
        $taskId = $request->route('task')?->id;
        $task = Task::find($taskId);

        // Verificar si el usuario autenticado es el propietario de la tarea
        if (!$task || $request->user()->id !== $task->user_id) {
            abort(403, 'No tienes permisos para acceder a esta tarea.');
        }

        return $next($request);
    }
}
