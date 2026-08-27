<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'producto';
    protected $primaryKey = 'id_pro';
    public $timestamps = false;
    protected $fillable = ['nom_pro', 'descripcion', 'precio', 'stock', 'categoria'];
    protected $casts = ['precio' => 'decimal:2', 'stock' => 'integer'];
}
