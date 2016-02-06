<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Counter extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'date', 
    		'calories', 
    		'distance', 
    		'counter_category_id', 
    	];
    
}
