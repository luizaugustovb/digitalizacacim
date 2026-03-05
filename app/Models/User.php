<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'nome',
        'codigo',
        'email',
        'password',
        'role',
        'ativo',
        'forcar_troca_senha',
        'telefone',
        'cpf',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'ativo' => 'boolean',
            'forcar_troca_senha' => 'boolean',
        ];
    }

    // Relacionamentos
    public function pedidosComoAtendente()
    {
        return $this->hasMany(Pedido::class, 'atendente_id');
    }

    public function pedidosComoGestor()
    {
        return $this->hasMany(Pedido::class, 'gestor_id');
    }

    public function convenios()
    {
        return $this->belongsToMany(Convenio::class, 'user_convenios');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
            ->withPivot('concedida')
            ->withTimestamps();
    }

    public function timelineLogs()
    {
        return $this->hasMany(TimelineLog::class);
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'ADMIN';
    }

    public function isGestor()
    {
        return $this->role === 'GESTOR';
    }

    public function isAtendente()
    {
        return $this->role === 'ATENDENTE';
    }

    public function hasPermission($permissionKey)
    {
        if ($this->isAdmin()) {
            return true;
        }
        return $this->permissions()->where('chave', $permissionKey)->exists();
    }

    public function setCodigoAttribute($value)
    {
        $this->attributes['codigo'] = $value ? strtoupper(trim($value)) : null;
    }
}
