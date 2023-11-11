<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *     title="Documentación para la API de tareas (INGCO)",
 *     version="1.0.0",
 *     @OA\Contact(
 *         email="meifer.elitepvpers@gmail.com"
 *    )
 * )
 */
/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="API endpoints for user authentication"
 * )
 */

/**
 * @OA\Tag(
 *     name="Tasks",
 *     description="API endpoints relacionado con las tareas. (se requiere que se le envie en el header el token de autenticación Bearrer). Verificar la documentación de la API de autenticación para más información"
 * )
 */
class TaskController extends Controller
{
    protected $ttl = 60 * 5; // ttl de 5 minutos

    private function getTasks()
    {
        $user_id = request()->get('user_id', null);
        $search = trim(request()->get('search', null));
        $currentPage = request()->get('page', 1);
        $claveTask = 'task-' . $user_id . $search . $currentPage;

        // validar con la bandera si aún debe conservarse el cache de tasks. true = se conserva, false = se elimina
        $flagValue = Cache::get('task-flag');
        if (!($flagValue !== null && $flagValue === true)) {
            Cache::forget($claveTask);
        }

        // Obtener la lista de productos desde la caché o desde la base de datos
        $ttl = $this->ttl;
        $tasks = Cache::remember($claveTask, $ttl, function () use ($user_id, $search) {
            return Task::with(['user', 'tags'])
                ->when($user_id, function ($query, $user_id) {
                    return $query->where('user_id', $user_id);
                })
                ->when($search, function ($query, $search) {
                    return $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                            ->orWhere('description', 'like', '%' . $search . '%')
                            ->orWhereHas('user', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            })
                            ->orWhereHas('tags', function ($q) use ($search) {
                                $q->where('name', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderByDesc('id')
                ->paginate(10);
        });

        Cache::put('task-flag', true, $ttl); // se guarda la bandera para conservar el cache de taskss

        return $tasks;
    }

    private function getTags()
    {
        // return Tag::orderBy('name')->get();
        $ttl = $this->ttl;
        $tags = Cache::remember('tags', $ttl, function () {
            return Tag::orderBy('name')->get();
        });
        return $tags;
    }

    private function getUsers()
    {
        // return User::orderBy('name')->get();
        $ttl = $this->ttl;
        $users = Cache::remember('users', $ttl, function () {
            return User::orderBy('name')->get();
        });
        return $users;
    }

    public function index()
    {
        $tasks = $this->getTasks();
        $users = $this->getUsers();
        return view('tasks.index', compact('tasks', 'users'));
    }

    public function createView()
    {
        $users = $this->getUsers();
        $tags = $this->getTags();
        return view('tasks.create', compact('users', 'tags'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'tags' => 'required|array|in:' . implode(',', Tag::pluck('id')->toArray()),
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
            'due_date' => 'fecha de vencimiento',
            'user_id' => 'usuario asignado',
            'tags' => 'etiquetas',
        ]);

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => $request->user_id,
        ]);
        Cache::forget('task-flag'); // se elimina la bandera para NO conservar el cache de tasks
        $task->tags()->sync($request->tags);
        return redirect()->route('tasks.index', $task->id);
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function update(Task $task)
    {
        $users = $this->getUsers();
        $tags = $this->getTags();
        return view('tasks.update', compact('task', 'users', 'tags'));
    }

    public function edit(Task $task, Request $request)
    {
        $this->authorize('update', $task);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'tags' => 'required|array|in:' . implode(',', Tag::pluck('id')->toArray()),
        ], [], [
            'name' => 'nombre',
            'description' => 'descripción',
            'due_date' => 'fecha de vencimiento',
            'user_id' => 'usuario asignado',
            'tags' => 'etiquetas',
        ]);

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => $request->user_id,
        ]);
        $task->tags()->sync($request->tags);
        Cache::forget('task-flag'); // se elimina la bandera para NO conservar el cache de tasks
        return redirect()->route('tasks.index', $task->id);
    }

    public function destroy(Task $task)
    {
        return view('tasks.destroy', compact('task'));
    }

    public function delete(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index');
    }

    /**
     * METODOS API
     */


    /**
     * @OA\Get(
     *     path="/api/tasks",
     *     operationId="getTasksAPI",
     *     tags={"Tasks"},
     *     summary="Obtener todas las tareas",
     *     description="Retorna todas las tareas disponibles",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de tareas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tasks"),
     *             @OA\Property(property="message", type="string", example="Tareas obtenidas correctamente"),
     *         ),
     *     ),
     *     security={
     *         {"passport": {}}
     *     }
     * )
     */
    public function getTasksAPI()
    {
        $tasks = $this->getTasks();
        return response()->json([
            'tasks' => $tasks,
            'message' => 'Tareas obtenidas correctamente'
        ]);
    }
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     operationId="getTagsAPI",
     *     tags={"Tags"},
     *     summary="Obtener todas las etiquetas",
     *     description="Retorna todas las etiquetas disponibles",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de etiquetas",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="tags"),
     *             @OA\Property(property="message", type="string", example="Etiquetas obtenidas correctamente"),
     *         ),
     *     ),
     *     security={
     *         {"passport": {}}
     *     }
     * )
     */
    public function getTagsAPI()
    {
        $tags = $this->getTags();
        return response()->json([
            'tags' => $tags,
            'message' => 'Etiquetas obtenidas correctamente'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/task",
     *     operationId="createTaskAPI",
     *     tags={"Tasks"},
     *     summary="Crear una nueva tarea",
     *     description="Crea una nueva tarea con la información proporcionada",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "due_date", "user_id", "tags"},
     *             @OA\Property(property="name", type="string", example="Nombre de la tarea"),
     *             @OA\Property(property="description", type="string", example="Descripción de la tarea"),
     *             @OA\Property(property="due_date", type="string", format="date-time", example="2023-01-01 12:00:00"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="string", example="1,2,3,6"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarea creada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="task"),
     *             @OA\Property(property="message", type="string", example="Tarea creada correctamente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"name": {"El campo name es requerido."}}),
     *         ),
     *     ),
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createTaskAPI(Request $request)
    {
        $data = $request->all();

        $data['tags'] = explode(',', $data['tags']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'tags' => 'required|array|in:' . implode(',', Tag::pluck('id')->toArray()),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $task = Task::create([
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => $request->user_id,
        ]);
        $task->tags()->sync($data['tags']);
        Cache::forget('task-flag'); // se elimina la bandera para NO conservar el cache de tasks
        return response()->json([
            'task' => $task->load(['user', 'tags']),
            'message' => 'Tarea creada correctamente'
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/users",
     *     operationId="getUsersAPI",
     *     tags={"Users"},
     *     summary="Obtener todos los usuarios",
     *     description="Retorna todos los usuarios disponibles",
     *     @OA\Response(
     *         response=200,
     *         description="Lista de usuarios",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="users"),
     *             @OA\Property(property="message", type="string", example="Usuarios obtenidos correctamente"),
     *         ),
     *     ),
     *     security={
     *         {"passport": {}}
     *     }
     * )
     */
    public function getUsersAPI()
    {
        $users = $this->getUsers();
        return response()->json([
            'users' => $users,
            'message' => 'Usuarios obtenidos correctamente'
        ]);
    }


    /**
     * @OA\Put(
     *     path="/api/task/{task}",
     *     operationId="updateTaskApi",
     *     tags={"Tasks"},
     *     summary="Actualizar una tarea existente",
     *     description="Actualiza una tarea existente con la información proporcionada",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID de la tarea a actualizar",
     *         required=true,
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "due_date", "user_id", "tags"},
     *             @OA\Property(property="name", type="string", example="Nombre actualizado de la tarea"),
     *             @OA\Property(property="description", type="string", example="Descripción actualizada de la tarea"),
     *             @OA\Property(property="due_date", type="string", format="date-time", example="2023-01-01 12:00:00"),
     *             @OA\Property(property="user_id", type="integer", example=1),
     *             @OA\Property(property="tags", type="string", example="1,2,3,6"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarea actualizada correctamente",
     *         @OA\JsonContent(
     *             @OA\Property(property="task"),
     *             @OA\Property(property="message", type="string", example="Tarea actualizada correctamente"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Error de validación",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Error de validación"),
     *             @OA\Property(property="errors", type="object", example={"name": {"El campo name es requerido."}}),
     *         ),
     *     ),
     * )
     *
     * @param \App\Models\Task $task
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTaskApi(Task $task, Request $request)
    {
        $data = $request->all();

        $data['tags'] = explode(',', $data['tags']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'due_date' => 'required|date',
            'user_id' => 'required|exists:users,id',
            'tags' => 'required|array|in:' . implode(',', Tag::pluck('id')->toArray()),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $task->update([
            'name' => $request->name,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'user_id' => $request->user_id,
        ]);
        $task->tags()->sync($data['tags']);
        Cache::forget('task-flag'); // se elimina la bandera para NO conservar el cache de tasks
        return response()->json([
            'task' => $task->load(['user', 'tags']),
            'message' => 'Tarea actualizada correctamente'
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}/delete",
     *     operationId="deleteTaskAPI",
     *     tags={"Tasks"},
     *     summary="Eliminar una tarea",
     *     description="Elimina una tarea existente",
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="ID de la tarea a eliminar",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tarea eliminada correctamente",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Tarea eliminada correctamente"),
     *         ),
     *     ),
     *     security={
     *         {"passport": {}}
     *     }
     * )
     */
    public function deleteTaskApi(Task $task)
    {
        $task->delete();
        return response()->json([
            'message' => 'Tarea eliminada correctamente'
        ]);
    }
}
