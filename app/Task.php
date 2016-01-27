<?php

namespace App;

use App\User;
use App\Category;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'deadline', 'description', 'category_id'];

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
    
}
