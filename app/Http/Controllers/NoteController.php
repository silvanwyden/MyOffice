<?php

namespace App\Http\Controllers;

use App\Note;
use App\Category;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\NoteRepository;
use DateTime;
use App\Session;
use DB;

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
    	
    	//base query
    	$notes = DB::table('notes')
    		->leftjoin('categories', 'notes.category_id', '=', 'categories.id')
    		->select(
    				'notes.title', 
    				'notes.id',
    				'notes.created_at',
    				'notes.updated_at', 
    				'categories.name as cname', 
    				'categories.css_class'
    				);
    	
    	//handle categories
    	if ($ses_category_id)
    		$notes->where('category_id', '=', $ses_category_id);
    	
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search) 
    			$request->session()->put('note_search', $request->search);
    		else
    			$request->session()->forget('note_search');
    	}
    	$search = $request->session()->get('note_search');
    	if (strlen($search) > 0)
    		$notes->where('notes.title', 'like', "%" . $search . "%");
    	
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
    	
    	$notes = $notes->orderBy('notes.'. $order, $dir)->orderBy('notes.title', 'ASC')->paginate(50);
    	
        return view('notes.index', [
        	'categories' => $categories,
            'notes' => $notes,
        	'order' => $order,
        	'dir' => $dir,
        	'category' => $user->note_category,
        	'search' => $search,
        	'page' => $page,
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
    	
    	return view('notes.update', [
    			'categories' => $categories,
    			'category_id' => $user->category_id,
    			'counter' => 0,
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
    	$priorities = Priority::All(['id', 'name']);
    	$stages = Stage::All(['id', 'name']);
    	$user = User::find($request->user()->id);
    	
    	//get next id
    	$notes = DB::table('Notes')
    	->leftjoin('categories', 'Notes.category_id', '=', 'categories.id')
    	->join('priorities', 'Notes.priority_id', '=', 'priorities.id')
    	->join('stages', 'Notes.stage_id', '=', 'stages.id')
    	->select('Notes.id as id')
    	->where('user_id', '=', $request->user()->id);
    	 
    	//handle stages
    	$ses_stage_id = $user->stage_id;
    	if ($ses_stage_id)
    		$notes->where('stage_id', '=', $ses_stage_id);
    	 
    	//handle categories
    	$ses_category_id = $user->category_id;
    	if ($ses_category_id)
    		$notes->where('category_id', '=', $ses_category_id);
    	 
    	//handle search
    	$search = $request->session()->get('search');
    	if (strlen($search) > 0)
    		$notes->where('notes.name', 'like', "%" . $search . "%");
    	 
    	//handle filters
    	$filter_deadline = $request->session()->get('filter_deadline');
    	if ($filter_deadline == 1)
    		$notes->where('deadline', '!=', '0000-00-00')->where('deadline', '<=', date('Y-m-d', time()));
    	 
    	//handle sort order
    	$order = $request->session()->get('order');
    	if (!$order)
    		$order = 'priority_id';
    	 
    	//handle sort direction
    	$dir = $request->session()->get('dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	$notes = $notes->orderBy('notes.'. $order, $dir)->orderBy('deadline', 'ASC')->orderBy('Notes.name', 'ASC')->paginate(50);
    	
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

    	return view('Notes.update', [
    			'categories' => $categories,
    			'priorities' => $priorities,
    			'stages' => $stages,
    			'Note' => $note,
    			'category_id' => False,
    			'previous_id' => $previous_id,
    			'next_id' => $next_id,
    			'counter' => $counter,
    			'total' => count($notes),
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
        
        $input = array(
	            'title' => $request->title,
	        	'description' => $request->description,
	        	'category_id' => $request->category,
	        );
        
        if ($request->note_id) {
        	
        	$note = Note::find($request->note_id);
        	$note->fill($input)->save();
        	$request->session()->flash('alert-success', 'Note was successful updated!');
        	
        	}
        else {
        	
	        $note = new Note();
    		$note->create($input);
	        $request->session()->flash('alert-success', 'Note was successful added!');
	        
	        }

	    $page = $request->session()->get('page');
        
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

        return redirect('/notes');
    }
    

    
}
