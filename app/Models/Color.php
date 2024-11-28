<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;

    protected $fillable = ['size_id', 'color_name', 'color_code'];

    public function size()
    {
        return $this->belongsTo(Size::class);
    }

    public function quantities()
    {
        return $this->hasOne(SizeColorQuantity::class);
    }
}
