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
use DateInterval;
use App\Session;
use DB;
use Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Fileentry;



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
		 
		//get basic objects
		$user = User::find($request->user()->id);
		$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
		$priorities = Priority::All(['id', 'name']);
		$stages = Stage::All(['id', 'name']);

		 
		//handle stages filter
		if ($request->stage_id)
			if ($request->stage_id > 0) {
			$request->session()->put('stage_id', $request->stage_id);
			$request->session()->put('stage', Stage::find($request->stage_id)->name);
		}
		else {
			$request->session()->put('stage_id', False);
			$request->session()->put('stage', "All Stages");
		}
		 
		$ses_stage_id = $request->session()->get('stage_id');
		 
		//handle categories filter
		if ($request->category_id)
			if ($request->category_id > 0) {
			$request->session()->put('category_id', $request->category_id);
			$request->session()->put('category', Category::find($request->category_id)->name);
		}
		else {
			$request->session()->put('category_id', False);
			$request->session()->put('category', "All Categories");
		}
		$ses_category_id = $request->session()->get('category_id');
		 
		//base query
		$tasks = DB::table('tasks')
		->leftjoin('categories', 'tasks.category_id', '=', 'categories.id')
		->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
		->join('stages', 'tasks.stage_id', '=', 'stages.id')
		->select(
				'tasks.name',
				'tasks.deadline',
				'tasks.id',
				'tasks.created_at',
				'tasks.updated_at',
				'categories.name as cname',
				'categories.css_class',
				'priorities.name as pname',
				'stages.name as sname',
                'tasks.is_urgent',
                DB::raw('LENGTH(tasks.description) as len_description')
		)
		->where('user_id', '=', $request->user()->id);
		 
		//handle stages
		if ($ses_stage_id)
			$tasks->where('stage_id', '=', $ses_stage_id);
		 
		//handle categories
		if ($ses_category_id)
			$tasks->where('category_id', '=', $ses_category_id);
		 
		//handle search
		if ($request->btn_search == "s") {
			if ($request->search)
				$request->session()->put('search', $request->search);
			else
				$request->session()->forget('search');
		}
		$search = $request->session()->get('search');
		if (strlen($search) > 0) {
			$tasks->where(function($query) use ($search)
            {
                $query->where('tasks.name', 'like', "%" . $search . "%")
                      ->orWhere('tasks.description', 'like', "%" . $search . "%");
            });
		}
		 
		//handle filters
		if ($request->filter_deadline == 1)
			$request->session()->put('filter_deadline', 1);
		elseif ($request->filter_deadline == -1)
		$request->session()->put('filter_deadline', 0);
		$filter_deadline = $request->session()->get('filter_deadline');
		if ($filter_deadline == 1)
			$tasks->where('deadline', '!=', '0000-00-00')->where('deadline', '<=', date('Y-m-d', time()));
		 
		//handle sort order
		if ($request->order)
			$request->session()->put('order', $request->order);
		$order = $request->session()->get('order');
		if (!$order)
			$order = 'priority_id';
		 
		//handle sort direction
		if ($request->dir)
			$request->session()->put('dir', $request->dir);
		$dir = $request->session()->get('dir');
		if (!$dir)
			$dir = 'ASC';
		 
		//handle pagination -> we don't want to lose the page
		if ($request->page)
			$request->session()->put('page', $request->page);
		$page = $request->session()->get('page');
		 
		if ($request->n)
			$request->session()->put('pagination_number', $request->n);
		elseif ($request->session()->get('pagination_number') < 1)
		$request->session()->put('pagination_number', 100);
		$pagination_number = $request->session()->get('pagination_number');
		 
		$tasks = $tasks->orderBy('tasks.'. $order, $dir)->orderBy('deadline', 'ASC')->orderBy('tasks.name', 'ASC')->paginate($pagination_number);
		 
		return view('tasks.index', [
				'categories' => $categories,
				'priorities' => $priorities,
				'stages' => $stages,
				'tasks' => $tasks,
				'order' => $order,
				'dir' => $dir,
				'stage' => $request->session()->get('stage'),
				'category' => $request->session()->get('category'),
				'search' => $search,
				'page' => $page,
				'filter_deadline' => $filter_deadline,
				]);

	}


	/**
	 * Create a new task: load date and forward to view
	 *
	 * @param  Request  $request
	 * @return view
	 */
	public function create(Request $request) {
		 
		$user = User::find($request->user()->id);
		$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
		$priorities = Priority::All(['id', 'name']);
		$stages = Stage::All(['id', 'name']);
		 
		return view('tasks.update', [
				'categories' => $categories,
				'priorities' => $priorities,
				'stages' => $stages,
				'category_id' => $request->session()->get('category_id'),
				'counter' => 0,
				'page' => $request->session()->get('page'),
				'filetab' => 0,
				])->withTask(new Task());

	}


	/**
	 * Update a new task: load date and forward to view
	 *
	 * @param  Request  $request, Task $task
	 * @return view
	 */
	public function update(Request $request, Task $task) {

		$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
		$priorities = Priority::All(['id', 'name']);
		$stages = Stage::All(['id', 'name']);
		$user = User::find($request->user()->id);
		 
		//get next id
		$tasks = DB::table('tasks')
		->leftjoin('categories', 'tasks.category_id', '=', 'categories.id')
		->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
		->join('stages', 'tasks.stage_id', '=', 'stages.id')
		->select('tasks.id as id')
		->where('user_id', '=', $request->user()->id);

		//handle stages
		$ses_stage_id = $request->session()->get('stage_id');
		if ($ses_stage_id)
			$tasks->where('stage_id', '=', $ses_stage_id);

		//handle categories
		$ses_category_id = $request->session()->get('category_id');
		if ($ses_category_id)
			$tasks->where('category_id', '=', $ses_category_id);

		//handle search
		$search = $request->session()->get('search');
		if (strlen($search) > 0) {
			$tasks->where(function($query) use ($search)
            {
                $query->where('tasks.name', 'like', "%" . $search . "%")
                      ->orWhere('tasks.description', 'like', "%" . $search . "%");
            });
		}

		//handle filters
		$filter_deadline = $request->session()->get('filter_deadline');
		if ($filter_deadline == 1)
			$tasks->where('deadline', '!=', '0000-00-00')->where('deadline', '<=', date('Y-m-d', time()));

		//handle sort order
		$order = $request->session()->get('order');
		if (!$order)
			$order = 'priority_id';

		//handle sort direction
		$dir = $request->session()->get('dir');
		if (!$dir)
			$dir = 'ASC';
		 
		$pagination_number = $request->session()->get('pagination_number');
		 
		$tasks = $tasks->orderBy('tasks.'. $order, $dir)->orderBy('deadline', 'ASC')->orderBy('tasks.name', 'ASC')->paginate($pagination_number);
		 
		$previous_id = 0;
		$next_id = 0;
		$counter = 0;
		$found = false;
		foreach ($tasks as $temp) {

			if ($found) {
				$next_id = $temp->id;
				break;
			}

			if ($temp->id == $task->id) {
				$found = true;
			}

			$counter++;
			if (!$found)
				$previous_id = $temp->id;

		}

		return view('tasks.update', [
				'categories' => $categories,
				'priorities' => $priorities,
				'stages' => $stages,
				'task' => $task,
				'category_id' => False,
				'previous_id' => $previous_id,
				'next_id' => $next_id,
				'counter' => $counter,
				'total' => count($tasks),
				'page' => $request->session()->get('page'),
				'filetab' => $request->filetab,
				])->withTask($task);
	}


	/**
	 * Validate AND Save/Crate a new task.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		
		$page = $request->session()->get('page');
		
		if ($request->save_edit_hidden == "save_edit_rename_filename") {
			$rename_id = $request->rename_file_id;
			$filename = $request->rename_file;
			
			$input = array('original_filename' => $filename);
			$entry = Fileentry::find($rename_id);
			$entry->fill($input)->save();
			$request->session()->flash('alert-success', 'File was successful renamed!');
			
			return redirect('/task/' . $request->task_id . '/update?page=' . $page . '&filetab=1');
		}
		
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
                'is_urgent' => $request->is_urgent or '0',
		);

		if ($request->task_id) {
			 
			$task = Task::find($request->task_id);
			$task->fill($input)->save();
			$request->session()->flash('alert-success', 'Task was successful updated!');
			 
		}
		else {
			 
			$task = $request->user()->tasks()->create($input);
			$request->session()->flash('alert-success', 'Task was successful added!');
			 
		}

		if ($request->save_edit or $request->save_edit_hidden) 
			return redirect('/task/' . $task->id . '/update?page=' . $page . "&filetab=" . $request->filetab);
		else
			return redirect('/tasks?page=' . $page);
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
		 
		//first we have to delelte all the files
		$model_id = 'task,' . $task->id;
		$entries = Fileentry::where('model_id', '=', $model_id)->get();
		foreach ($entries as $entry) {
			$file = Storage::disk('local')->delete($entry->filename);
			$fname = $entry->original_filename;
			$entry->delete();
			Log::info('Deleted Files:' . $fname);
		}
		 
		$this->authorize('destroy', $task);
		$task->delete();

		$request->session()->flash('alert-success', 'Task was successful deleted!');
		$page = $request->session()->get('page');

		return redirect('/tasks?page=' . $page);
	}


	/**
	 * Set the given task to state 'done'.
	 *
	 * @param  Request  $request
	 * @param  Task  $task
	 * @return Response
	 */
	public function done(Request $request, Task $task)
	{
			
		$task->stage_id = 3;
		$task->save();
		 
		$request->session()->flash('alert-success', 'Task was successful changed to stage done!');
		$page = $request->session()->get('page');
		 
		return redirect('/tasks?page=' . $page);
	}

	public function upload(Request $request, Task $task)
	{
		Log::info('Uploading Files!');

		$file = $request->file;
		$extension = $file->getClientOriginalExtension();
		Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
		$entry = new Fileentry();
		$entry->mime = $file->getClientMimeType();
		$entry->original_filename = $file->getClientOriginalName();
		$entry->filename = $file->getFilename().'.'.$extension;
		$entry->model_id = "task," . $task->id;
		$entry->save();
		 
		return ['success' => false, 'data' => 200];
	}
	
	/**
	 * Set the given task deadline + one week'.
	 *
	 * @param  Request  $request
	 * @param  Task  $task
	 * @return Response
	 */
	public function plus_week(Request $request, Task $task)
	{
			
		$now = new DateTime();
		$deadline = $now->add(new DateInterval('P1W'))->format('Y-m-d');
		$task->deadline = $deadline;
		$task->save();
			
		$request->session()->flash('alert-success', 'Deadline of task was successful changed to next week!');
		$page = $request->session()->get('page');
			
		return redirect('/tasks?page=' . $page);
	}
	
	/**
	 * Set the given task deadline + one week'.
	 *
	 * @param  Request  $request
	 * @param  Task  $task
	 * @return Response
	 */
	public function plus_month(Request $request, Task $task)
	{
			
		$now = new DateTime();
		$deadline = $now->add(new DateInterval('P1M'))->format('Y-m-d');
		$task->deadline = $deadline;
		$task->save();
			
		$request->session()->flash('alert-success', 'Deadline of task was successful changed to next month!');
		$page = $request->session()->get('page');
			
		return redirect('/tasks?page=' . $page);
	}


}
