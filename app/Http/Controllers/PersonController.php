<?php

namespace App\Http\Controllers;

use App\Person;
use App\Category;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PersonRepository;
use DateTime;
use App\Session;
use DB;

class PersonController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $persons;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(PersonRepository $persons)
    {
        $this->middleware('auth');

        $this->persons = $persons;
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
    	$categories = Category::All(['id', 'name']);
    	
    	//handle categories filter
    	
    	//base query
    	$persons = DB::table('persons')
    	->leftjoin('categories', 'persons.category_id', '=', 'categories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'persons.id',
    			'persons.lastname',
    			'persons.surname',
    			'persons.phone',
    			'persons.mobile',
    			'persons.mail',
    			'persons.birthdate',
    			'persons.created_at',
    			'persons.updated_at',
    			'categories.name as cname',
    			'categories.css_class'
    	);
    	
    	
    	
    	$persons = $persons->paginate(50);
    	
        return view('persons.index', [
        	'persons' => $persons,
        	'categories' => $categories,
        	'order' => '',
        	'dir' => '',
        	'page' => '',
        	'category' => '',
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
    	$categories = Category::All(['id', 'name']);
    	
    	return view('persons.update', [
    			'categories' => $categories,
    			'category_id' => '',
    			])->withPerson(new Person());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Person $person) {
    	
    	$categories = Category::All(['id', 'name']);

    	return view('persons.update', [
    			'categories' => $categories,
				'person' => $person,
    			'category_id' => False,
    			])->withPerson($person);
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
    	if ($request->birthdate) {
    		$date = DateTime::createFromFormat('d.m.Y', $request->birthdate);
    		$date = $date->format('Y-m-d');
    	}
    
    	$input = array(
    			'lastname' => $request->lastname,
    			'surname' => $request->surname,
    			'phone' => $request->phone,
    			'mobile' => $request->mobile,
    			'mail' => $request->mail,
    			'birthdate' => $date,
    			'category_id' => $request->category,
    	);
    
    	if ($request->person_id) {
    		 
    		$person = Person::find($request->person_id);
    		$person->fill($input)->save();
    		$request->session()->flash('alert-success', 'Person was successful updated!');
    		 
    	}
    	else {
    		 
    		$person = new Person();
    		$person->create($input);
    		$request->session()->flash('alert-success', 'Person was successful added!');
    		 
    	}
    
    	//$page = $request->session()->get('page');
    	 
    	//return redirect('/persons?page=' . $page);
    	return redirect('/persons?page=');
    }

    
    
    
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Person $person)
    {

    	$person->delete();
    
    	$request->session()->flash('alert-success', 'Person was successful deleted!');
    
    	return redirect('/persons');
    }
    
    
}
