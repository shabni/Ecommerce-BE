<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeColorQuantity extends Model
{
    use HasFactory;

    protected $fillable = ['color_id', 'quantity'];

    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
