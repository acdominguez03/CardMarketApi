<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Collection;

class Card extends Model
{
    use HasFactory;

    public function collections()
    {
        return $this->belongsToMany(Collection::class);
    }
}
