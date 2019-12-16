<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Classh extends Model
{
    protected $table = 'Classes';
    protected $primaryKey = 'cid';
    public $timestamps = false;
}
