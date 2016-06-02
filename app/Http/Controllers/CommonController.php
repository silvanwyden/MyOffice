<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Http\Requests;
use App\User;
use App\Category;
use App\Countercategory;
use Illuminate\Http\Request;
use DateTime;
use App\Session;
use DB;
use Log;

class CommonController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $countercategories;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');

    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function about(Request $request)
    {
    	
        return view('common.about', [
        ]);
        
    }    
    
}
