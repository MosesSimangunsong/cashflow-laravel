<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    protected $table = 'casflows';
    protected $fillable = ['user_id', 'title', 'description', 'is_finished', 'cover'];
    public $timestamps = true;
}
