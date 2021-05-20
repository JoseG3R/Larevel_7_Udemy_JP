<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receta extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'preparacion',
        'ingredientes',
        'imagen',
        'categoria_id'
    ];

    #Obtiene la categoría de la receta vía FK
    public function categoria()
    {
        return $this->belongsTo(CategoriaReceta::class);
    }
    #Obtiene la información del usuario vía FK

    public function autor()
    {
        return $this->belongsTo(User::class, 'user_id'); //FK de esta tabla
    }
}
