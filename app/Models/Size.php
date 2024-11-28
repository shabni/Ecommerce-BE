<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasFactory;

    protected $fillable = ['item_id', 'size_name', 'quantity'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function colors()
    {
        return $this->hasMany(Color::class);
    }
}

