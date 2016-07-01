<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Fileentry extends Model
{
    //
    
	protected $fillable = array('original_filename', 'thumb');
	
	
}
