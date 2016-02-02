<?php

namespace App\Http\Controllers;

use App\Task;
use App\Category;
use App\Priority;
use App\Stage;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use DateTime;
use App\Session;

class TaskController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $tasks;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(TaskRepository $tasks)
    {
        $this->middleware('auth');

        $this->tasks = $tasks;
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
    	
    	$user = User::find($request->user()->id);
    	
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);

    	$order = 'priority_id';
    	if ($request->order)
    		$order = $request->order;
    	
    	$dir = 'ASC';
    	if ($request->dir)
    		$dir = $request->dir;
    	
    	
    	//handle stages filter
    	if ($request->stage_id)
    		if ($request->stage_id > 0) {
    			$user->stage_id = $request->stage_id;
	    		$user->stage = Stage::find($request->stage_id)->name;
	    		$user->save();
    		}
    		else {
    			$user->stage_id = False;
    			$user->stage = "--all Stages--";
    			$user->save();
    		}
    	
    	$ses_stage_id = $user->stage_id;
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
	    		$user->category_id = $request->category_id;
	    		$user->category = Category::find($request->category_id)->name;
	    		$user->save();
	    	}
	    	else {
	    		$user->category_id = False;
	    		$user->category = "--all Categories--";
	    		$user->save();
    	}
    	 
    	$ses_category_id = $user->category_id;
    	 
    	if ($ses_stage_id && $ses_category_id) 
    		$tasks = Task::where('user_id', '=', $request->user()->id)->where('stage_id', '=', $ses_stage_id)->where('category_id', '=', $ses_category_id)->orderBy($order, $dir)->orderBy('deadline', 'ASC')->paginate(200);
    	elseif ($ses_stage_id) 
    		$tasks = Task::where('user_id', '=', $request->user()->id)->where('stage_id', '=', $ses_stage_id)->orderBy($order, $dir)->orderBy('deadline', 'ASC')->paginate(200);
    	elseif ($ses_category_id)
    		$tasks = Task::where('user_id', '=', $request->user()->id)->where('category_id', '=', $ses_category_id)->orderBy($order, $dir)->orderBy('deadline', 'ASC')->paginate(200);
    	else 
    		$tasks = Task::where('user_id', '=', $request->user()->id)->orderBy($order, $dir)->orderBy('deadline', 'ASC')->paginate(200);
    	
        return view('tasks.index', [
        	'categories' => $categories,
        	'priorities' => $priorities,
        	'stages' => $stages,
            'tasks' => $tasks,
        	'order' => $order,
        	'dir' => $dir,
        	'stage' => $user->stage,
        	'category' => $user->category,
        ]);
        
    }

    
    public function create(Request $request) {
    
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);
    
    	return view('tasks.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			])->withTask(new Task());
    	 
    }
    
    
    public function update(Request $request, Task $task) {
    
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);
    
    	return view('tasks.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			'task' => $task,
    			])->withTask($task);
    }
    

    /**
     * Create a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
        ]);

        $deadline = False;
        if ($request->deadline) {
        	$deadline = DateTime::createFromFormat('d.m.Y', $request->deadline);
        	$deadline = $deadline->format('Y-m-d');
        }
        
        $input = array(
	            'name' => $request->name,
	        	'deadline' => $deadline,
	        	'description' => $request->description,
	        	'category_id' => $request->category,
	        	'priority_id' => $request->priority,
	        	'stage_id' => $request->stage,
	        );
        
        if ($request->task_id) {
        	
        	$task = Task::find($request->task_id);
        	$task->fill($input)->save();
        	$request->session()->flash('alert-success', 'Task was successful updated!');
        	
        	}
        else {
        	
	        $request->user()->tasks()->create($input);
	        $request->session()->flash('alert-success', 'Task was successful added!');
	        
	        }

        	
        	
        return redirect('/tasks');
    }
    
    

    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Task $task)
    {
        $this->authorize('destroy', $task);

        $task->delete();

        return redirect('/tasks');
    }
    
    
    public function done(Request $request, Task $task)
    {
    	    
    	$task->stage_id = 3;
    	$task->save();
    	
    	$request->session()->flash('alert-success', 'Task was successful changed to stage done!');
    
    	return redirect('/tasks');
    }
    
}
