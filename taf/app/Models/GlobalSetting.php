<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $table = 'global_settings';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['glob_wall', 'glob_mime'];
}