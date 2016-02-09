<?php

namespace App\Http\Controllers;

use App\Person;
use App\Category;
use App\Tag;
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
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
    		$user->person_category_id = $request->category_id;
    		$user->person_category = Category::find($request->category_id)->name;
    		$user->save();
    	}
    	else {
    		$user->person_category_id = False;
    		$user->person_category = "--all Categories--";
    		$user->save();
    	}
    	$ses_category_id = $user->person_category_id;
    	
    	//base query
    	$persons = DB::table('persons')
    	->leftjoin('categories', 'persons.category_id', '=', 'categories.id')
    	//->join('priorities', 'tasks.priority_id', '=', 'priorities.id')
    	->select(
    			'persons.id',
    			'persons.lastname',
    			'persons.surname',
    			DB::raw('CONCAT(persons.lastname, " ", persons.surname) AS searchname'),
    			'persons.phone',
    			'persons.mobile',
    			'persons.mail',
    			'persons.birthdate',
    			'persons.birthday',
    			'persons.created_at',
    			'persons.tag_ids',
    			'persons.updated_at',
    			'categories.name as cname',
    			'categories.css_class'
    	);
    	
    	//handle categories
    	if ($ses_category_id)
    		$persons->where('category_id', '=', $ses_category_id);
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('person_order', $request->order);
    	$order = $request->session()->get('person_order');
    	if (!$order)
    		$order = 'lastname';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('person_dir', $request->dir);
    	$dir = $request->session()->get('person_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('person_page', $request->page);
    	$page = $request->session()->get('person_page');
    	
    	$persons = $persons->orderBy($order, $dir)->paginate(50);
    	
        return view('persons.index', [
        	'persons' => $persons,
        	'categories' => $categories,
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'category' => $user->person_category,
        	'tags' => $tags
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
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
    	return view('persons.update', [
    			'categories' => $categories,
    			'category_id' => $user->person_category_id,
    			'tags' => $tags
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
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$tags_sel = Tag::find(explode(",", $person->tag_ids));

    	return view('persons.update', [
    			'categories' => $categories,
				'person' => $person,
    			'category_id' => False,
    			'tags' => $tags,
    			'tags_sel' => $tags_sel
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
    	//print "tags:" . $request->tags; //-> tags:1,4,6
    	$date = False;
    	$birthday = False;
    	if ($request->birthdate) {
    		$date = DateTime::createFromFormat('d.m.Y', $request->birthdate);
    		$birthday = $date->format('m-d');
    		$date = $date->format('Y-m-d');
    	}
    
    	$input = array(
    			'lastname' => $request->lastname,
    			'surname' => $request->surname,
    			'phone' => $request->phone,
    			'mobile' => $request->mobile,
    			'mail' => $request->mail,
    			'birthdate' => $date,
    			'birthday' => $birthday,
    			'category_id' => $request->category,
    			'tag_ids' => $request->tags,
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
    
    	$page = $request->session()->get('person_page');
    	 
    	return redirect('/persons?page=' . $page);
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
