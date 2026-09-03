<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $table = 'detalles_venta';
    protected $primaryKey = 'id_detalle_venta';
    public $timestamps = false;
    protected $fillable = ['id_venta', 'id_receta', 'cantidad', 'precio_unitario', 'subtotal', 'personalizacion'];
    protected $casts = ['precio_unitario' => 'decimal:2', 'subtotal' => 'decimal:2'];

    public function venta()
    {
        return $this->belongsTo(Sale::class, 'id_venta', 'id_venta');
    }

    public function receta()
    {
        return $this->belongsTo(Recipe::class, 'id_receta', 'id_receta');
    }
}
