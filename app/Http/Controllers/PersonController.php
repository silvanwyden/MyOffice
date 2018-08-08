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
use Log;
use Mail;
use Redirect;
use Illuminate\Http\Response;

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
    	$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
    		$request->session()->put('person_category_id', $request->category_id);
    		$request->session()->put('person_category', Category::find($request->category_id)->name);
    	}
    	else {
    		$request->session()->put('person_category_id', False);
    		$request->session()->put('person_category', "All Categories");
    	}
    	$ses_category_id = $request->session()->get('person_category_id');
    	
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
    	
    	if ($request->filter_birthday == 1)
    		$request->session()->put('filter_birthday', 1);
    	elseif ($request->filter_birthday == -1)
    	$request->session()->put('filter_birthday', 0);
    	$filter_birthday = $request->session()->get('filter_birthday');
    	if ($filter_birthday == 1) {
    		$persons->where('birthday', '>', 0);
    		$order = 'birthday';
    	}
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('person_page', $request->page);
    	$page = $request->session()->get('person_page');
    	
    	if ($request->n)
    		$request->session()->put('pagination_number', $request->n);
    	elseif ($request->session()->get('pagination_number') < 1)
    		$request->session()->put('pagination_number', 100);
    	$pagination_number = $request->session()->get('pagination_number');
    	
    	$persons = $persons->orderBy($order, $dir)->orderBy('searchname')->paginate($pagination_number);
    	
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
        return view('persons.index', [
        	'persons' => $persons,
        	'categories' => $categories,
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'category' => $request->session()->get('person_category'),
        	'category_id' => $request->session()->get('person_category_id'),
        	'filter_parent' => $filter_parent,
        	'filter_child' => $filter_child,
        	'filter_birthday' => $filter_birthday,
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
    	$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$parents = Person::All(['id', 'lastname', 'surname']);
    	
    	return view('persons.update', [
    			'categories' => $categories,
    			'category_id' => $request->session()->get('person_category_id'),
    			'tags' => $tags,
    			'tags_sel' => array(),
    			'parents' => $parents,
    			'counter' => 0,
    			'children' => array(),
    			'page' => $request->session()->get('person_page'),
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
    	$categories = Category::where('is_note', '=', 0)->orderBy('seq')->get();
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$parents = Person::All(['id', 'lastname', 'surname']);
    	
    	
    	//get next id
    	$persons = DB::table('persons')
    	->leftjoin('categories', 'persons.category_id', '=', 'categories.id')
    	->select('persons.id as id');
    	
    	//handle categories
    	$ses_category_id = $request->session()->get('person_category_id');
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
    	
    	$filter_birthday = $request->session()->get('filter_birthday');
    	if ($filter_birthday == 1) {
    		$persons->where('birthday', '>', 0);
    		$order = 'birthday';
    	}
    	
    	$pagination_number = $request->session()->get('pagination_number');
    	    		
    	$persons = $persons->orderBy($order, $dir)->orderBy('searchname')->paginate($pagination_number);
   
    	$previous_id = 0;
    	$next_id = 0;
    	$counter = 0;
    	$found = false;
    	foreach ($persons as $temp) {
    	
    		if ($found) {
    			$next_id = $temp->id;
    			break;
    		}
    	
    		if ($temp->id == $person->id) {
    			$found = true;
    		}
    	
    		$counter++;
    		if (!$found)
    			$previous_id = $temp->id;
    	
    	}
    	
    	$tags_sel = Tag::find(explode(",", $person->tag_ids));
    	
    	$children = Person::where('parent_id', '=', $person->id)->orderBy('searchname')->get();
    	
    	return view('persons.update', [
    			'categories' => $categories,
				'person' => $person,
    			'category_id' => False,
    			'tags' => $tags,
    			'tags_sel' => $tags_sel,
    			'parents' => $parents,
    			'next_id' => $next_id,
    			'previous_id' => $previous_id,
    			'counter' => $counter,
    			'total' => count($persons),
    			'children' => $children,
    			'page' => $request->session()->get('person_page'),
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
    	
    	//check if tags belong all to the category_id
    	foreach(explode(",", $request->tags) as $temp) {
    		$t = Tag::find($temp);
    		if ($t) {
    			if ($t->category_id != $request->category)
    				return Redirect::back()->withErrors('Tag ' . $t->name . ' is from the wrong category!')->withInput();
    		}
    	}
    	
    	$date = False;
    	$birthday = False;
    	if ($request->birthdate) {
    		$date = DateTime::createFromFormat('d.m.Y', $request->birthdate);
    		$birthday = $date->format('m-d');
    		$date = $date->format('Y-m-d');
    	}
    	
    	$gender = '';
    	if ($request->gender)
    		$gender = $request->gender;
    
    	$input = array(
    			'lastname' => $request->lastname,
    			'surname' => $request->surname,
    			'searchname' => $request->lastname . ' ' . $request->surname,
    			'gender' => $gender,
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
    		$person = $person->create($input);
    		$request->session()->flash('alert-success', 'Person was successful added!');
    		 
    	}
    
    	$page = $request->session()->get('person_page');
    	 
    	if ($request->save_edit or $request->save_edit_hidden)
    		return redirect('/person/' . $person->id . '/update?page=' . $page);
    	else
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
    	$page = $request->session()->get('person_page');
    	$request->session()->flash('alert-success', 'Person was successful deleted!');
    
    	return redirect('/persons?page=' . $page);
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
		    	$ses_category_id = $this->temp_request->session()->get('person_category_id');
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
    
    
    //scheduler for cron job mail
    public function sendBirthdaysMail() {
    	Log::info('Calculation who is having birthday today and sending mail!');
    	
    	$birthdays = Person::where('birthday', '=', date('m-d', time()))->get();
    	
    	if (count($birthdays) > 0)
    		{
    		
    		$message = '';
    		$names = '';
    			
    		foreach ($birthdays as $birthday) {
    		
    			Log::info($birthday->searchname);
    			
    			$names .= $birthday->searchname . ", ";
    			
    			}
    			
    			
    		Mail::send('emails.welcome', [ 'birthdays' => $names ], function ($message) {
    			$message->from('it@wyden.com', 'Today birthdays');
    					
    			$message->to('info@wyden.com');
    			//->cc('silvan@wyden.com');
    			});
    			 
    		}
    
    }
    
    
    public function search(Request $request) {
    
    	Log::info("search request for persons");
    
    	$result = '';
    	$q = $request->q;
    
    	$persons = Person::where('searchname', 'like', '%' . $q . '%')
    		->where('id', '!=', $request->active_id)
    		->where('parent_id', '<=' , 0)
			->orderBy('searchname')->limit(10)->get();
    	 
    	if (count($persons) > 0) {
    		foreach ($persons as $person)
    			$result .= '{"key": ' . $person->id . ',"value": "' . $person->searchname . '"},';
    
    		$result = rtrim($result, ",");
    	}
    
    	return '[' . $result . ']';
    
    }
    
    
}
