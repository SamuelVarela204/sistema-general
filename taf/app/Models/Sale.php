<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;
    protected $fillable = ['id_usu_cajero', 'total', 'descuento', 'metodo_pago', 'estado', 'fecha_venta'];
    protected $casts = ['fecha_venta' => 'datetime', 'total' => 'decimal:2', 'descuento' => 'decimal:2'];

    public function cajero()
    {
        return $this->belongsTo(User::class, 'id_usu_cajero', 'id_usu');
    }

    public function detalles()
    {
        return $this->hasMany(SaleDetail::class, 'id_venta', 'id_venta');
    }
}
