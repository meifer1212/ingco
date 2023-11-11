@extends('app')

@section('title', 'Nueva Tarea')

@section('content')
    <div class="p-5">
        <header class="text-center">
            <span class="font-semibold text-xl">Visualizando Tarea: {{ $task->name }}</span>
        </header>
        <div class="grid gap-4 justify-evenly w-full mt-5">
            <div class="flex gap-4">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium ">Tarea</label>
                    <input title="No es posible modificar" type="name" id="name" name="name" readonly
                        value="{{ $task->name }}"
                        class="bg-gray-50 border border-gray-300 cursor-not-allowed  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                        placeholder="Cenar en ...">
                </div>
                <div>
                    <label for="due_date" class="block mb-2 text-sm font-medium ">Fecha de Vencimiento</label>
                    <input title="No es posible modificar" type="datetime-local" id="due_date" name="due_date" readonly
                        value="{{ $task->due_date->format('Y-m-d\TH:i') }}"
                        class="bg-gray-50 border border-gray-300 cursor-not-allowed  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  ">
                </div>
            </div>
            <div>
                <label for="user_id" class="block mb-2 text-sm font-medium ">Creador</label>
                <input title="No es posible modificar" type="text" id="user_id" name="user_id" readonly
                    value="{{ $task->user->name }}"
                    class="bg-gray-50 border border-gray-300 cursor-not-allowed  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  ">
            </div>
            <div>
                <label for="tags[]" class="block mb-2 text-sm font-medium ">Etiquetas</label>
                <input title="No es posible modificar" type="text" id="tags[]" name="tags[]" readonly
                    value="{{ $task->tags->pluck('name')->join(', ') }}"
                    class="bg-gray-50 border border-gray-300 cursor-not-allowed  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  ">
            </div>
            <div>
                <label for="description" class="block mb-2 text-sm font-medium ">Descripción</label>
                <textarea title="No es posible modificar" name="description" id="description" cols="30" rows="4"
                    maxlength="255" readonly
                    class="bg-gray-50 border border-gray-300 cursor-not-allowed  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">{{ $task->description }}</textarea>
            </div>
        </div>
        <br>
        <div class="w-full flex gap-4">
            <a href="#" onclick="history.back();"
                class="text-white  bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Regresar</a>
            @can('update', $task)
                <a href="{{ route('tasks.update', $task->id) }}"
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Editar</a>
            @else
                <a href="#"
                    class="text-white line-through bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center">Editar</a>
            @endcan
            @can('delete', $task)
                <a href="{{ route('tasks.destroy', $task->id) }}"
                    class="text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Eliminar</a>
            @else
                <a href="#"
                    class="text-white line-through bg-red-700 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center">Eliminar</a>
            @endcan
        </div>
    </div>
@endsection
