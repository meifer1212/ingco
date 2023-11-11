<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    private function getTasks()
    {
        $user_id = request()->get('user_id', null);
        $search = trim(request()->get('search', null));

        $query = Task::with(['user', 'tags'])
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

        return $query;
    }

    private function getTags()
    {
        return Tag::orderBy('name')->get();
    }

    private function getUsers()
    {
        return User::orderBy('name')->get();
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
}
