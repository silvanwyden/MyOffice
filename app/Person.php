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
    		'birthday', 
    		'category_id',
    		'tag_ids',
    	];
    
    
    /**
     * Get the category that belongs to the task.
     */
    public function tags()
    {
    	
    	$tags_sel = Tag::find(explode(",", $tag_ids));

    	return $tags_sel;
    }
    
}
