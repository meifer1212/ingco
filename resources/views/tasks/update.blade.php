@extends('app')

@section('title', 'Nueva Tarea')

@section('content')
    <div class="p-5">
        <header class="text-center">
            <span class="font-semibold text-xl">Editando Tarea: {{ $task->name }}</span>
        </header>
        <form action="{{ route('tasks.edit', $task->id) }}" method="POST" class="mt-5">
            @csrf
            @method('PUT')
            <div class="grid gap-4 justify-evenly w-full">
                <div class="flex gap-4">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium ">Tarea</label>
                        <input type="name" id="name" name="name" value="{{ $task->name }}"
                            class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="Cenar en ..." required>
                    </div>
                    <div>
                        <label for="due_date" class="block mb-2 text-sm font-medium ">Fecha de Vencimiento</label>
                        <input type="datetime-local" id="due_date" name="due_date"
                            value="{{ $task->due_date->format('Y-m-d\TH:i') }}"
                            class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  "
                            required>
                    </div>
                </div>
                <div>
                    <label for="user_id" class="block mb-2 text-sm font-medium ">Creador</label>
                    <select name="user_id" id="user_id" value="{{ $task->user_id }}"
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Seleccione una opción</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($user->id == $task->user_id)>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tags" class="block mb-2 text-sm font-medium ">Etiquetas *</label>
                    <select name="tags[]" id="tags[]" required multiple value="{{ $task->tags->pluck('id') }}"
                        title="Seleccione una o varias opciones (Presione Ctrl para seleccionar varias)"
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}" @selected(in_array($tag->id, $task->tags->pluck('id')->toArray()))>{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium ">Descripción</label>
                    <textarea name="description" id="description" cols="30" rows="4" maxlength="255"
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ $task->description }}</textarea>
                </div>
            </div>
            <div class="flex w-full mt-3 gap-4">
                <a href="#" onclick="history.back();"
                    class="text-white  bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Regresar</a>
                @can('delete', $task)
                    <a href="{{ route('tasks.destroy', $task->id) }}"
                        class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Eliminar</a>
                @else
                    <a href="#"
                        class="text-white line-through bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center">Eliminar</a>
                @endcan
                @can('update', $task)
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Editar
                    </button>
                @else
                    <button type="submit" disable
                        class="text-white line-through bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Editar
                    </button>
                @endcan
            </div>
        </form>
        <div>
            {{-- errores --}}
            @if ($errors->any())
                <div class="mt-5">
                    <div class="font-medium text-red-600">
                        Han ocurrido algunos errores:
                    </div>
                    <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>
            @endif
        </div>
    </div>
@endsection
