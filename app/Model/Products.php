<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    /**
     * @var string
     * define table
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'image_url', 'manufacturer', 'description', 'model', 'year', 'engine', 'mileage', 'price'
    ];
}
