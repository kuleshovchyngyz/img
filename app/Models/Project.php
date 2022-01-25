<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'status',
        'folder',
        'user_id',
    ];


    public function images(){
        return $this->hasMany(Image::class);
    }
}
