<?php

namespace App;

use App\User;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'title',  
    		'description', 
    		'category_id', 
    		'tag_ids'
    	];
    
    
    public function getFiles()
    {
    	$model_id = "note," . $this->id;
    	return Fileentry::where('model_id', '=', $model_id)->orderBy('original_filename')->get();
    }


    
}
