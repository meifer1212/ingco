@extends('app')

@section('title', 'Tareas')

@section('content')
    <div class="p-5 w-[150vh]">
        <header class="text-center">
            <span class="font-semibold text-xl">Listado de Tareas</span>
        </header>
        <div class="w-full flex mt-6">
            <div>
                {{-- input type search & select by user_id --}}
                <form action="{{ route('tasks.index') }}" method="GET">
                    <div class="flex">
                        <div class="flex mr-2 w-52">
                            <input type="search" name="search" id="search"
                                class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                placeholder="Buscar" value="{{ request('search') }}">
                        </div>
                        <div class="flex mr-2 w-52">
                            <select name="user_id" id="user_id"
                                class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="">Filtro por Creador</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @selected($user->id == request('user_id'))>{{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Buscar</button>
                        {{-- boton borrar filtro si hay --}}
                        @if (request('search') || request('user_id'))
                            <a href="{{ route('tasks.index') }}"
                                class="text-white bg-red-700 ml-3 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center">Borrar
                                Filtro</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="ml-auto">
                <a href="{{ route('tasks.create') }}"
                    class="text-white mt-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full sm:w-auto px-5 py-2.5 text-center">Nueva
                    Tarea</a>
            </div>
        </div>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-3">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            #
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tarea
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Tags
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de Creación
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha de Vencimiento
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Creador
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tasks as $task)
                        <tr class="bg-white border-b hover:bg-gray-50 ">
                            <td class="px-6 py-4">
                                {{ $task->id }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $task->name }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $task->tags->pluck('name')->join(', ') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $task->created_at->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $task->due_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                {{ $task->user->name }}
                            </td>
                            <td class="flex items-center px-6 py-4">
                                <a href="{{ route('tasks.show', $task->id) }}"
                                    class="font-medium text-gray-600 hover:underline">Ver</a>
                                @can('update', $task)
                                    <a href="{{ route('tasks.update', $task->id) }}"
                                        class="font-medium text-blue-600 hover:underline ms-3">Editar</a>
                                @else
                                    <span class="font-medium text-blue-600 line-through ms-3">Editar</span>
                                @endcan
                                @can('delete', $task)
                                    <a href="{{ route('tasks.destroy', $task->id) }}"
                                        class="font-medium text-red-600  hover:underline ms-3">Eliminar</a>
                                @else
                                    <span class="font-medium text-red-600 line-through ms-3">Eliminar</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4">
                                <div class="flex justify-center items-center">
                                    <span class="font-medium py-8 text-gray-400 text-xl">No hay tareas registradas</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div>
        {{ $tasks->withQueryString()->links('pagination::simple-tailwind') }}
    </div>
@endsection
