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
use Excel;

class PersonController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $persons;
    public $temp_request;

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
    		$user->person_category = "All Categories";
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
    			'persons.searchname',
    			'persons.phone',
    			'persons.mobile',
    			'persons.mail',
    			'persons.birthdate',
    			'persons.birthday',
    			'persons.created_at',
    			'persons.tag_ids',
    			'persons.updated_at',
    			'persons.parent_id',
    			'categories.name as cname',
    			'categories.css_class'
    	);
    	
    	//handle categories
    	if ($ses_category_id)
    		$persons->where('category_id', '=', $ses_category_id);
    	
    	//handle search tags
    	$tags_sel = array();
    	if ($request->btn_search == "s") {
    		if ($request->search)
    			$request->session()->put('person_search', $request->search);
    		else
    			$request->session()->forget('person_search');
    	}
    	$search = $request->session()->get('person_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$persons->where('tag_ids', 'like', "%" . $s . "%");
    	}
    	
    	//handle search text
    	if ($request->btn_search == "s") {
    		if ($request->search_text)
    			$request->session()->put('person_search_text', $request->search_text);
    		else
    			$request->session()->forget('person_search_text');
    	}
    	$search_text = $request->session()->get('person_search_text');
    	if (strlen($search_text) > 0)
    		$persons->where('persons.searchname', 'like', "%" . $search_text . "%");
    	
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
    	
    	//handle filters
    	if ($request->filter_parent == 1)
    		$request->session()->put('filter_parent', 1);
    	elseif ($request->filter_parent == -1)
    	$request->session()->put('filter_parent', 0);
    	$filter_parent = $request->session()->get('filter_parent');
    	if ($filter_parent == 1)
    		$persons->where('parent_id', '=', 0);
    	
    	if ($request->filter_child == 1)
    		$request->session()->put('filter_child', 1);
    	elseif ($request->filter_child == -1)
    	$request->session()->put('filter_child', 0);
    	$filter_child = $request->session()->get('filter_child');
    	if ($filter_child == 1)
    		$persons->where('parent_id', '>', 0);
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('person_page', $request->page);
    	$page = $request->session()->get('person_page');
    	
    	$persons = $persons->orderBy($order, $dir)->paginate(100);
    	
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
        return view('persons.index', [
        	'persons' => $persons,
        	'categories' => $categories,
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'category' => $user->person_category,
        	'tags' => $tags,
        	'filter_parent' => $filter_parent,
        	'filter_child' => $filter_child,
        	'tags' => $tags,
        	'tags_sel' => $tags_sel,
        	'search_text' => $search_text,
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
    	$parents = Person::All(['id', 'lastname', 'surname']);
    	
    	return view('persons.update', [
    			'categories' => $categories,
    			'category_id' => $user->person_category_id,
    			'tags' => $tags,
    			'tags_sel' => array(),
    			'parents' => $parents
    			])->withPerson(new Person());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Person $person) {
    	
    	$user = User::find($request->user()->id);
    	$categories = Category::All(['id', 'name']);
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$tags_sel = Tag::find(explode(",", $person->tag_ids));
    	$parents = Person::All(['id', 'lastname', 'surname']);
    	
    	
    	/*
    	//base query
    	$persons = DB::table('persons')
    	->leftjoin('categories', 'persons.category_id', '=', 'categories.id');
    	
    	//handle categories
    	$ses_category_id = $user->person_category_id;
    	if ($ses_category_id)
    		$persons->where('category_id', '=', $ses_category_id);
    	 
    	//handle search tags
    	$search = $request->session()->get('person_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$persons->where('tag_ids', 'like', "%" . $s . "%");
    	}
    	 
    	//handle search text
    	$search_text = $request->session()->get('person_search_text');
    	if (strlen($search_text) > 0)
    		$persons->where('persons.searchname', 'like', "%" . $search_text . "%");
    	 
    	//handle sort order
    	$order = $request->session()->get('person_order');
    	if (!$order)
    		$order = 'lastname';
    	 
    	//handle sort direction
    	$dir = $request->session()->get('person_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	 
    	//handle filters
    	$filter_parent = $request->session()->get('filter_parent');
    	if ($filter_parent == 1)
    		$persons->where('parent_id', '=', 0);
    	
    	$filter_child = $request->session()->get('filter_child');
    	if ($filter_child == 1)
    		$persons->where('parent_id', '>', 0);
    	 
    	print $order;
    	
    	$persons = $persons->where('persons.id', '>', $person->id)->orderBy($order, $dir)->min('persons.id');
    	

    	
    	print "next:" . $persons;*/

    	return view('persons.update', [
    			'categories' => $categories,
				'person' => $person,
    			'category_id' => False,
    			'tags' => $tags,
    			'tags_sel' => $tags_sel,
    			'parents' => $parents
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
    			'searchname' => $request->lastname . ' ' . $request->surname,
    			'phone' => $request->phone,
    			'mobile' => $request->mobile,
    			'mail' => $request->mail,
    			'birthdate' => $date,
    			'birthday' => $birthday,
    			'category_id' => $request->category,
    			'tag_ids' => $request->tags,
    			'parent_id' => $request->parent_id,
    			'salutation' => $request->salutation,
    			'street' => $request->street,
    			'plz' => $request->plz,
    			'city' => $request->city,
    			'country' => $request->country
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
    
    
    
    public function excel(Request $request) {
    	
    	$this->temp_request = $request;
    	
    	Excel::create('Laravel Excel', function($excel) {

		    $excel->sheet('Sheetname', function($sheet) {

		    	$user = User::find($this->temp_request->user()->id);
		    	
		    	//base query
		    	$persons = DB::table('persons')
		    	->leftjoin('categories', 'persons.category_id', '=', 'categories.id')
		    	->select(
		    			'persons.salutation',
		    			'persons.lastname',
		    			'persons.surname',
		    			'persons.street',
		    			'persons.plz',
		    			'persons.city',
		    			'persons.country',
		    			'persons.phone',
		    			'persons.mobile',
		    			'persons.mail',
		    			'persons.birthdate',
		    			'persons.tag_ids',
		    			'persons.parent_id',
		    			'categories.name as cname'
		    	);
		    	
		    	//handle categories
		    	$ses_category_id = $user->person_category_id;
		    	if ($ses_category_id)
		    		$persons->where('category_id', '=', $ses_category_id);
		    	 
		    	//handle search tags
		    	$search = $this->temp_request->session()->get('person_search');
		    	if (strlen($search) > 0) {
		    		$search_array = explode(",", $search);
		    		$tags_sel = Tag::find($search_array);
		    		foreach(explode(",", $search) as $s)
		    			$persons->where('tag_ids', 'like', "%" . $s . "%");
		    	}
		    	 
		    	//handle search text
		    	$search_text = $this->temp_request->session()->get('person_search_text');
		    	if (strlen($search_text) > 0)
		    		$persons->where('persons.searchname', 'like', "%" . $search_text . "%");
		    	 
		    	//handle sort order
		    	$order = $this->temp_request->session()->get('person_order');
		    	if (!$order)
		    		$order = 'lastname';
		    	 
		    	//handle sort direction
		    	$dir = $this->temp_request->session()->get('person_dir');
		    	if (!$dir)
		    		$dir = 'ASC';
		    	 
		    	//handle filters
		    	$filter_parent = $this->temp_request->session()->get('filter_parent');
		    	if ($filter_parent == 1)
		    		$persons->where('parent_id', '=', 0);
		    	
		    	$filter_child = $this->temp_request->session()->get('filter_child');
		    	if ($filter_child == 1)
		    		$persons->where('parent_id', '>', 0);
		    	 
		    	$persons = $persons->orderBy($order, $dir)->get();
		    	
		    	$data = json_decode(json_encode((array) $persons), true);
		    	
		        $sheet->with($data);
		        
		    });
		
		})->export('xlsx');

    }
    
    
}
