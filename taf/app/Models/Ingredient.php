<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $table = 'ingredientes';
    protected $primaryKey = 'id_ingrediente';
    public $timestamps = false;
    protected $fillable = ['nombre_ingrediente', 'tipo', 'descripcion', 'unidad_medida', 'costo_unitario', 'stock_actual', 'stock_minimo', 'estado'];

    public function recetas()
    {
        return $this->belongsToMany(Recipe::class, 'detalles_receta', 'id_ingrediente', 'id_receta')
                    ->withPivot('cantidad_requerida', 'unidad_medida');
    }

    public function movimientos()
    {
        return $this->hasMany(InventoryMovement::class, 'id_ingrediente', 'id_ingrediente');
    }

    public function alertas()
    {
        return $this->hasMany(StockAlert::class, 'id_ingrediente', 'id_ingrediente');
    }
}
