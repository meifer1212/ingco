@extends('app')

@section('title', 'Nueva Tarea')

@section('content')
    <div class="p-5" style="width: 400px">
        <header class="text-center">
            <span class="font-semibold text-xl">Eliminar Tarea: {{ $task->name }}</span>
        </header>
        <div class="mt-4">
            <span>Estás seguro de eliminar la tarea '{{ $task->name }}'?</span>
        </div>
        <br>
        <form action="{{ route('tasks.delete', $task->id) }}" method="POST">
            <div class="w-full flex gap-4">
                @csrf
                @method('DELETE')
                <a href="#" onclick="history.back();"
                    class="text-white  w-full bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Regresar</a>
                <button type="submit"
                    class="text-white w-full bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center ">Eliminar
                    Tarea</button>
            </div>
        </form>
    </div>
@endsection
