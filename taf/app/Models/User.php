<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $table = 'usuarios';
    protected $primaryKey = 'id_usu';
    public $timestamps = false;
    protected $fillable = ['id_rol', 'nom_com', 'usu_con', 'imagen', 'telefono', 'correo', 'direccion', 'alergias', 'descripcion', 'estado'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'usu_con',
        'remember_token',
    ];

    public function getAuthPasswordName()
    {
        return 'usu_con';
    }

    public function getAuthPassword()
    {
        return (string) $this->usu_con;
    }

    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    public function getNameAttribute()
    {
        return $this->nom_com;
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'id_rol', 'id_rol');
    }

    public function getRoleNameAttribute()
    {
        $idRol = (int) ($this->attributes['id_rol'] ?? $this->id_rol ?? 2);

        $roleName = trim((string) ($this->attributes['nombre_rol'] ?? ''));
        if ($roleName === '' && $this->relationLoaded('role')) {
            $roleName = trim((string) optional($this->role)->nombre_rol);
        }

        if ($roleName !== '') {
            return strtolower($roleName);
        }

        return match ($idRol) {
            1 => 'admin',
            2 => 'cliente',
            3 => 'inventario',
            4 => 'gerente',
            5 => 'cajero',
            6 => 'trabajador',
            default => 'cliente',
        };
    }

    public function hasRole(string ...$roles): bool
    {
        $requested = array_map('strtolower', $roles);
        $roleName = strtolower((string) $this->role_name);

        return in_array($roleName, $requested, true);
    }
}
