<?php

namespace App;

use App\User;
use App\Category;
use App\Priority;
use App\Stage;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    		'name', 
    		'deadline', 
    		'description', 
    		'category_id', 
    		'priority_id',
    		'stage_id',
    	];

    /**
     * Get the user that owns the task.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Get the category that belongs to the task.
     */
    public function category()
    {
    	return $this->belongsTo(Category::class);
    }
    
    /**
     * Get the priority that belongs to the task.
     */
    public function priority()
    {
    	return $this->belongsTo(Priority::class);
    }
    
    /**
     * Get the stage that belongs to the task.
     */
    public function stage()
    {
    	return $this->belongsTo(Stage::class);
    }
    
}
