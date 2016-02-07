<?php

namespace App\Http\Controllers;

use App\Counter;
use App\Countercategory;
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
    	->leftjoin('countercategories', 'counters.counter_category_id', '=', 'countercategories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'counters.id',
    			'counters.date',
    			'counters.calories',
    			'counters.distance',
    			'counters.counter_category_id',
    			'counters.created_at',
    			'counters.updated_at',
    			'countercategories.name as cname',
    			'countercategories.css_class'
    	);
    	
    	$counters = $counters->paginate(50);
    	
        return view('counters.index', [
        	'counters' => $counters,
        	'order' => '',
        	'dir' => '',
        	'page' => '',
        ]);
        
    }
    
    
    /**
     * Create a new task: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	 
    	$countercategories = Countercategory::All(['id', 'name']);
    	
    	return view('counters.update', [
    			'countercategories' => $countercategories,
    			'counter_category_id' => False,
    			])->withCounter(new Counter());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Counter $counter) {
    	
    	$countercategories = Countercategory::All(['id', 'name']);

    	return view('counters.update', [
    			'countercategories' => $countercategories,
				'counter' => $counter,
    			'counter_category_id' => False,
    			])->withCounter($counter);
    }
    
    
    /**
     * Validate AND Save/Crate a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    	
    	$date = False;
    	if ($request->date) {
    		$date = DateTime::createFromFormat('d.m.Y', $request->date);
    		$date = $date->format('Y-m-d');
    	}
    
    	$input = array(
    			'date' => $date,
    			'counter_category_id' => $request->category,
    	);
    
    	if ($request->counter_id) {
    		 
    		$counter = Counter::find($request->counter_id);
    		$counter->fill($input)->save();
    		$request->session()->flash('alert-success', 'Counter was successful updated!');
    		 
    	}
    	else {
    		 
    		$counter = new Counter();
    		$counter->create($input);
    		$request->session()->flash('alert-success', 'Counter was successful added!');
    		 
    	}
    
    	$page = $request->session()->get('page');
    	 
    	return redirect('/counters?page=' . $page);
    }

    
    
    
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Counter $counter)
    {
    	print "counter" . $counter->date;  
    	alert("counter" . $counter->date);
    
    	//$task->delete();
    
    	$request->session()->flash('alert-success', 'Task was successful deleted!');
    
    	return redirect('/counters');
    }
    
    
}
