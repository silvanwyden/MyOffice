<?php

namespace App\Http\Controllers;

use App\Note;
use App\Category;
use App\Http\Requests;
use App\User;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\NoteRepository;
use DateTime;
use App\Session;
use DB;
use Log;
use Redirect;

class NoteController extends Controller
{
    /**
     * The Note repository instance.
     *
     * @var NoteRepository
     */
    protected $notes;

    /**
     * Create a new controller instance.
     *
     * @param  NoteRepository  $notes
     * @return void
     */
    public function __construct(NoteRepository $notes)
    {
        $this->middleware('auth');

        $this->Notes = $notes;
    }

    /**
     * Display a list of all of the user's Note.
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
    	if ($request->category_id)
    		if ($request->category_id > 0) {
	    		$user->note_category_id = $request->category_id;
	    		$user->note_category = Category::find($request->category_id)->name;
	    		$user->save();
	    	}
	    	else {
	    		$user->note_category_id = False;
	    		$user->note_category = "All Categories";
	    		$user->save();
    	}
    	$ses_category_id = $user->note_category_id;
    	
    	if ($ses_category_id)
    		$tags = Tag::where('category_id', '=', $ses_category_id)->orderBy('name')->get();
    	else
    		$tags = Tag::all()->sortBy('name');
    	
    	//base query
    	$notes = DB::table('notes')
    		->leftjoin('categories', 'notes.category_id', '=', 'categories.id')
    		->select(
    				'notes.title', 
    				'notes.id',
    				'notes.created_at',
    				'notes.updated_at', 
    				'notes.tag_ids',
    				'categories.name as cname', 
    				'categories.css_class'
    				);
    	
    	//handle categories
    	if ($ses_category_id)
    		$notes->where('category_id', '=', $ses_category_id);
    	
    	//handle search tags
    	$tags_sel = array();
    	if ($request->btn_search == "s") {
    		if ($request->search)
    			$request->session()->put('note_search', $request->search);
    		else
    			$request->session()->forget('note_search');
    	}
    	$search = $request->session()->get('note_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$notes->where('tag_ids', 'like', "%," . $s . ",%");
    	}
    	
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search_text) 
    			$request->session()->put('note_search_text', $request->search_text);
    		else
    			$request->session()->forget('note_search_text');
    	}
    	$search_text = $request->session()->get('note_search_text');
    	if (strlen($search_text) > 0)
    		$notes->where('notes.title', 'like', "%" . $search_text . "%")->orWhere('notes.description', 'like', "%" . $search_text . "%");
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('note_order', $request->order);
    	$order = $request->session()->get('note_order');
    	if (!$order)
    		$order = 'title';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('note_dir', $request->dir);
    	$dir = $request->session()->get('note_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('note_page', $request->page);
    	$page = $request->session()->get('note_page');
    	
    	if ($request->n)
    		$request->session()->put('pagination_number', $request->n);
    	elseif ($request->session()->get('pagination_number') < 1)
    	$request->session()->put('pagination_number', 100);
    	$pagination_number = $request->session()->get('pagination_number');
    	
    	$notes = $notes->orderBy('notes.'. $order, $dir)->orderBy('notes.title', 'ASC')->paginate($pagination_number);
    	
        return view('notes.index', [
        	'categories' => $categories,
            'notes' => $notes,
        	'order' => $order,
        	'dir' => $dir,
        	'category' => $user->note_category,
        	'category_id' => $user->note_category_id,
        	'search' => $search,
        	'page' => $page,
        	'tags' => $tags,
        	'tags_sel' => $tags_sel,
        	'search_text' => $search_text,
        ]);
        
    }

    
    /**
     * Create a new Note: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	
    	$user = User::find($request->user()->id);
    	$categories = Category::All(['id', 'name']);
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
    	return view('notes.update', [
    			'categories' => $categories,
    			'category_id' => $user->note_category_id,
    			'counter' => 0,
    			'tags' => $tags,
    			'tags_sel' => array(),
    			'page' => $request->session()->get('note_page'),
    			])->withNote(new Note());
    	 
    }
    
    
    /**
     * Update a new Note: load date and forward to view
     *
     * @param  Request  $request, Note $note
     * @return view
     */
    public function update(Request $request, Note $note) {
    
    	$categories = Category::All(['id', 'name']);
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$user = User::find($request->user()->id);
    	
    	//get next id
    	$notes = DB::table('notes')
    	->leftjoin('categories', 'notes.category_id', '=', 'categories.id')
    	->select('notes.id as id');
    	    	 
    	//handle categories
    	$ses_category_id = $user->note_category_id;
    	if ($ses_category_id)
    		$notes->where('category_id', '=', $ses_category_id);

    	//handle search tags
    	$search = $request->session()->get('note_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$notes->where('tag_ids', 'like', "%," . $s . ",%");
    	}
    	 
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search_text)
    			$request->session()->put('note_search_text', $request->search_text);
    		else
    			$request->session()->forget('note_search_text');
    	}
    	$search_text = $request->session()->get('note_search_text');
    	if (strlen($search_text) > 0)
    		$notes->where('notes.title', 'like', "%" . $search_text . "%")->orWhere('notes.description', 'like', "%" . $search_text . "%");

    	//handle sort order
    	$order = $request->session()->get('note_order');
    	if (!$order)
    		$order = 'title';
    	 
    	//handle sort direction
    	$dir = $request->session()->get('note_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	$pagination_number = $request->session()->get('pagination_number');
    	
    	$notes = $notes->orderBy('notes.'. $order, $dir)->orderBy('notes.title', 'ASC')->paginate($pagination_number);
    	
    	$previous_id = 0;
    	$next_id = 0;
    	$counter = 0;
    	$found = false;
    	foreach ($notes as $temp) {
    		
    		if ($found) {
    			$next_id = $temp->id;
    			break;
    		}
    		
    		if ($temp->id == $note->id) {
    			$found = true;
    		}
    		
    		$counter++;
    		if (!$found)
    			$previous_id = $temp->id;
    		
    	}

    	return view('notes.update', [
    			'categories' => $categories,
    			'note' => $note,
    			'category_id' => False,
    			'tags' => $tags,
    			'tags_sel' => $tags_sel = Tag::find(explode(",", $note->tag_ids)),
    			'previous_id' => $previous_id,
    			'next_id' => $next_id,
    			'counter' => $counter,
    			'total' => count($notes),
    			'page' => $request->session()->get('note_page'),
    			])->withNote($note);
    }

    /**
     * Validate AND Save/Crate a new Note.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:255',
        ]);
        
        //check if tags belong all to the category_id
        foreach(explode(",", $request->tags) as $temp) {
        	$t = Tag::find($temp);
        	if ($t) {
        		if ($t->category_id != $request->category)
        			return Redirect::back()->withErrors('Tag ' . $t->name . ' is from the wrong category!')->withInput();
        	}
        }
        	
        
        
        $input = array(
	            'title' => $request->title,
	        	'description' => $request->description,
	        	'category_id' => $request->category,
        		'tag_ids' => ',' . $request->tags . ',',
	        );
        
        if ($request->note_id) {
        	
        	$note = Note::find($request->note_id);
        	$note->fill($input)->save();
        	$request->session()->flash('alert-success', 'Note was successful updated!');
        	
        	}
        else {
        	
	        $note = new Note();
    		$note = $note->create($input);
	        $request->session()->flash('alert-success', 'Note was successful added!');
	        
	        }

	    $page = $request->session()->get('note_page');

	    if ($request->save_edit)
        	return redirect('/note/' . $note->id . '/update');
	    else
        	return redirect('/notes?page=' . $page);
    }
    
    

    /**
     * Destroy the given Note.
     *
     * @param  Request  $request
     * @param  Note  $note
     * @return Response
     */
    public function destroy(Request $request, Note $note)
    {

        $note->delete();
        
        $request->session()->flash('alert-success', 'Note was successful deleted!');
        $page = $request->session()->get('note_page');

        return redirect('/notes?page=' . $page);
    }
    
    
    
    

    
}
