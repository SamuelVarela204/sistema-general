@extends('layouts.app')
@section('content')
<h1>Usuarios</h1>

@if(auth()->user()->hasRole('admin'))
<section class="form-card">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="form-grid">
            <input name="nom_com" placeholder="Nombre" required>
            <input type="email" name="correo" placeholder="Correo" required>
            <input type="password" name="contrasena" placeholder="Contraseña" minlength="6" required>
            <select name="id_rol">
                @foreach($roles as $role)
                    <option value="{{ $role->id_rol }}">{{ $role->nombre_rol }}</option>
                @endforeach
            </select>
            <select name="estado">
                <option value="activo">activo</option>
                <option value="inactivo">inactivo</option>
            </select>
        </div>
        <button class="button">Crear usuario</button>
    </form>
</section>
@endif

<section class="table-wrap">
    <table>
        <tr>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Rol</th>
            <th>Estado</th>
            @if(auth()->user()->hasRole('admin'))<th>Acciones</th>@endif
        </tr>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->nom_com }}</td>
            <td>{{ $user->correo }}</td>
            <td>{{ $user->role->nombre_rol ?? '' }}</td>
            <td>{{ $user->estado }}</td>
            @if(auth()->user()->hasRole('admin'))
            <td>
                <form method="POST" action="{{ route('admin.users.update', $user) }}" style="display:inline-block; margin-right: 0.5rem;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="nom_com" value="{{ $user->nom_com }}" required style="width: 120px; margin-bottom: 0.25rem; display:block;">
                    <input type="email" name="correo" value="{{ $user->correo }}" required style="width: 150px; margin-bottom: 0.25rem; display:block;">
                    <select name="id_rol" style="width: 120px; margin-bottom: 0.25rem; display:block;">
                        @foreach($roles as $role)
                            <option value="{{ $role->id_rol }}" {{ $user->id_rol == $role->id_rol ? 'selected' : '' }}>{{ $role->nombre_rol }}</option>
                        @endforeach
                    </select>
                    <select name="estado" style="width: 100px; margin-bottom: 0.25rem; display:block;">
                        <option value="activo" {{ $user->estado === 'activo' ? 'selected' : '' }}>activo</option>
                        <option value="inactivo" {{ $user->estado === 'inactivo' ? 'selected' : '' }}>inactivo</option>
                    </select>
                    <button class="button" type="submit">Guardar</button>
                </form>

                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="link-danger" type="submit" {{ auth()->id() === $user->id_usu ? 'disabled' : '' }}>Borrar</button>
                </form>
            </td>
            @endif
        </tr>
        @endforeach
    </table>
    {{ $users->links() }}
</section>
@endsection
