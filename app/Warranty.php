<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Warranty extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'title',  
    		'date_purchase', 
    		'warranty_months', 
    		'date_warranty',
    		'category_id',
    		'tag_ids',
    		'location'    		
    	];
    
    
    public function getFiles()
    {
    	$model_id = "warranty," . $this->id;
    	return Fileentry::where('model_id', '=', $model_id)->orderBy('original_filename')->get();
    }
    
      
}
