<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryMovement extends Model
{
    protected $table = 'movimientos_inventario';
    protected $primaryKey = 'id_movimiento';
    public $timestamps = false;
    protected $fillable = ['id_ingrediente', 'tipo_movimiento', 'cantidad', 'descripcion', 'id_usu_responsable', 'fecha_movimiento'];
    protected $casts = ['fecha_movimiento' => 'datetime'];

    public function ingrediente()
    {
        return $this->belongsTo(Ingredient::class, 'id_ingrediente', 'id_ingrediente');
    }

    public function responsable()
    {
        return $this->belongsTo(User::class, 'id_usu_responsable', 'id_usu');
    }
}
