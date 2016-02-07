<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
	
	
	protected $table = 'persons';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'lastname', 
    		'surname', 
    		'phone', 
    		'mobile',
    		'mail',
    		'birthdate', 
    		'category_id',
    	];
    
}
