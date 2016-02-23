<?php

namespace App;

use App\Category;
use Illuminate\Database\Eloquent\Model;

class Passpack extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'url', 
    		'user', 
    		'password', 
    		'category_id', 
    		'name',
    		'description',
    	];

    
}
