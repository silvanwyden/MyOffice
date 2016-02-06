<?php

namespace App\Http\Controllers;

use App\Task;
use App\Category;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CounterRepository;
use DateTime;
use App\Session;
use DB;

class CounterController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $counters;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(CounterRepository $counters)
    {
        $this->middleware('auth');

        $this->counters = $counters;
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
    	
    	//base query
    	$counters = DB::table('counters')
    	//->leftjoin('categories', 'tasks.category_id', '=', 'categories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'counters.date',
    			'counters.calories',
    			'counters.distance',
    			'counters.counter_category_id',
    			'counters.created_at',
    			'counters.updated_at'
    			 
    	);
    	
    	$counters = $counters->paginate(50);
    	
        return view('counters.index', [
        	'counters' => $counters,
        	'order' => '',
        	'dir' => '',
        	'page' => '',
        ]);
        
    }

    
}
