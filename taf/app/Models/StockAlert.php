<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAlert extends Model
{
    protected $table = 'alertas_stock';
    protected $primaryKey = 'id_alerta';
    public $timestamps = false;
    protected $fillable = ['id_ingrediente', 'stock_actual', 'stock_minimo', 'estado_alerta', 'fecha_alerta', 'fecha_resolucion'];
    protected $casts = ['fecha_alerta' => 'datetime', 'fecha_resolucion' => 'datetime'];

    public function ingrediente()
    {
        return $this->belongsTo(Ingredient::class, 'id_ingrediente', 'id_ingrediente');
    }
}
