@extends('app')

@section('title', 'Nueva Tarea')

@section('content')
    <div class="p-5">
        <header class="text-center">
            <span class="font-semibold text-xl">Formulario Crear Tarea</span>
        </header>
        <form action="{{ route('tasks.create') }}" method="POST" class="mt-5">
            @csrf
            <div class="grid gap-4 justify-evenly w-full">
                <div class="flex gap-4">
                    <div>
                        <label for="name" class="block mb-2 text-sm font-medium ">Tarea *</label>
                        <input type="name" id="name" name="name"
                            class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "
                            placeholder="Cenar en ..." required>
                    </div>
                    <div>
                        <label for="due_date" class="block mb-2 text-sm font-medium ">Fecha de Vencimiento *</label>
                        <input type="datetime-local" id="due_date" value="{{ date('Y-m-d\TH:i') }}" name="due_date"
                            class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5  "
                            required>
                    </div>
                </div>
                <div>
                    <label for="user_id" class="block mb-2 text-sm font-medium ">Creador *</label>
                    <select name="user_id" id="user_id" value="{{ auth()->id() }}" required
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Seleccione una opción</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected($user->id == auth()->id())>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="tags" class="block mb-2 text-sm font-medium ">Etiquetas *</label>
                    <select name="tags[]" id="tags[]" required multiple
                        title="Seleccione una o varias opciones (Presione Ctrl para seleccionar varias)"
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        @foreach ($tags as $tag)
                            <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="description" class="block mb-2 text-sm font-medium ">Descripción</label>
                    <textarea name="description" id="description" cols="30" rows="4" maxlength="255"
                        class="bg-gray-50 border border-gray-300  text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"></textarea>
                </div>
            </div>
            <button type="submit"
                class="text-white mt-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm w-full px-5 py-2.5 text-center ">Crear
                Tarea</button>
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
