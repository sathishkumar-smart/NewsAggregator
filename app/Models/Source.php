<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Source extends Model
{
    use HasFactory;


    protected $fillable = ['name', 'source_id'];

    public function articles()
    {
        return $this->hasMany(Article::class);
    }
}
