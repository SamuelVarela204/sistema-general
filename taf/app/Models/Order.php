<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'pedido';
    protected $primaryKey = 'id_ped';
    const CREATED_AT = 'fecha_pedido';
    const UPDATED_AT = null;
    protected $fillable = ['estado', 'total'];
    protected $casts = ['fecha_pedido' => 'datetime', 'total' => 'decimal:2'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_usu', 'id_usu');
    }
}
