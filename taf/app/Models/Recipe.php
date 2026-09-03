<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $table = 'recetas';
    protected $primaryKey = 'id_receta';
    public $timestamps = false;
    protected $fillable = ['nombre_receta', 'descripcion', 'precio_base', 'personalizable', 'estado', 'id_usu_creador', 'fecha_creacion', 'fecha_actualizacion'];

    public function ingredientes()
    {
        return $this->belongsToMany(Ingredient::class, 'detalles_receta', 'id_receta', 'id_ingrediente')
                    ->withPivot('cantidad_requerida', 'unidad_medida');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'id_usu_creador', 'id_usu');
    }

    public function ventas()
    {
        return $this->hasMany(SaleDetail::class, 'id_receta', 'id_receta');
    }
}
