<?php

namespace App\Http\Controllers;

use App\Task;
use App\Category;
use App\Priority;
use App\Stage;
use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TaskRepository;
use DateTime;

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

    	$order = 'priority_id';
    	if ($request->order)
    		$order = $request->order;
    	
    	$dir = 'ASC';
    	if ($request->dir)
    		$dir = $request->dir;

    	$tasks = Task::where('user_id', '=', $request->user()->id)->orderBy($order, $dir)->paginate(20);
    	
        return view('tasks.index', [
            'tasks' => $tasks,
        	'order' => $order,
        	'dir' => $dir,
        ]);
        
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
        	$deadline = DateTime::createFromFormat('m/d/Y', $request->deadline);
        	$deadline = $deadline->format('Y-m-d');
        }
        
        $request->user()->tasks()->create([
            'name' => $request->name,
        	'deadline' => $deadline,
        	'description' => $request->description,
        	'category_id' => $request->category,
        	'priority_id' => $request->priority,
        	'stage_id' => $request->stage,
        ]);

        return redirect('/tasks');
    }
    
    public function create(Request $request) {
    
    	$categories = Category::All(['id', 'name']);
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);

    	return view('tasks.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			]);
    	
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
    			]);
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
}
