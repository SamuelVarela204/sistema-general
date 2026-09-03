@extends('layouts.app')
@section('content')
<h1>Pedidos</h1>
<section class="table-wrap">
    <table>
        <tr>
            <th>ID</th>
            <th>Cliente</th>
            <th>Fecha</th>
            <th>Estado</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        @foreach($orders as $order)
        <tr>
            <td>{{ $order->id_ped }}</td>
            <td>{{ $order->user->nom_com ?? 'N/D' }}</td>
            <td>{{ $order->fecha_pedido?->format('d/m/Y H:i') }}</td>
            <td>{{ $order->estado }}</td>
            <td>${{ number_format($order->total, 2) }}</td>
            <td>
                @if(auth()->user()->hasRole('admin', 'gerente', 'inventario'))
                <form method="POST" action="{{ route('admin.orders.update', $order) }}" style="display:inline-block; margin-right:0.5rem;">
                    @csrf
                    @method('PUT')
                    <select name="estado" style="display:block; margin-bottom:0.25rem;">
                        <option value="pendiente" {{ $order->estado === 'pendiente' ? 'selected' : '' }}>pendiente</option>
                        <option value="preparando" {{ $order->estado === 'preparando' ? 'selected' : '' }}>preparando</option>
                        <option value="enviado" {{ $order->estado === 'enviado' ? 'selected' : '' }}>enviado</option>
                        <option value="entregado" {{ $order->estado === 'entregado' ? 'selected' : '' }}>entregado</option>
                        <option value="cancelado" {{ $order->estado === 'cancelado' ? 'selected' : '' }}>cancelado</option>
                    </select>
                    <input type="hidden" name="total" value="{{ $order->total }}">
                    <button type="submit" class="button">Actualizar</button>
                </form>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route('admin.orders.destroy', $order) }}" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="link-danger" type="submit">Borrar</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    {{ $orders->links() }}
</section>
@endsection
