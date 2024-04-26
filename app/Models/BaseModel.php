<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use MongoDB\Laravel\Eloquent\Model;

/**
 * Define uma model base comum para todas as models.
 */
abstract class BaseModel extends Model
{
    use HasFactory;

    // protected $connection = 'mongodb';
}
