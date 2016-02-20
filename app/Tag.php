<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Note;

class Tag extends Model
{
	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'tags';
	
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name',];
    
    public function getNumberNotes() {
    	
    	return Note::where('tag_ids', 'like', "%" . $this->id . "%")->count();
    	
    }
    


}
