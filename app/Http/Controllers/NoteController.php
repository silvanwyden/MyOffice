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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Fileentry;

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
	    		$request->session()->put('note_category_id', $request->category_id);
	    		$request->session()->put('note_category',Category::find($request->category_id)->name);
	    	}
	    	else {
	    		$request->session()->put('note_category_id', False);
	    		$request->session()->put('note_category', "All Categories");
    	}
    	$ses_category_id = $request->session()->get('note_category_id');
    	
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
    	if (strlen($search_text) > 0) {
    		$notes->where(function($query) use ($search_text)
    		{
    			$query->where('notes.title', 'like', "%" . $search_text . "%")
    			->orWhere('notes.description', 'like', "%" . $search_text . "%");
    		});
    	}
    	
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
        	'category' => $request->session()->get('note_category'),
        	'category_id' => $request->session()->get('note_category_id'),
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
    			'category_id' => $request->session()->get('note_category_id'),
    			'counter' => 0,
    			'tags' => $tags,
    			'tags_sel' => array(),
    			'page' => $request->session()->get('note_page'),
    			'filetab' => 0,
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
    	$ses_category_id = $request->session()->get('note_category_id');
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
    	if (strlen($search_text) > 0) {
    		$notes->where(function($query) use ($search_text)
    		{
    			$query->where('notes.title', 'like', "%" . $search_text . "%")
    			->orWhere('notes.description', 'like', "%" . $search_text . "%");
    		});
    	}

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
    			'filetab' => $request->filetab,
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
    	
    	$page = $request->session()->get('page');
    	
    	if ($request->save_edit_hidden == "save_edit_rename_filename") {
    		$rename_id = $request->rename_file_id;
    		$filename = $request->rename_file;
    			
    		$input = array('original_filename' => $filename);
    		$entry = Fileentry::find($rename_id);
    		$entry->fill($input)->save();
    		$request->session()->flash('alert-success', 'File was successful renamed!');
    			
    		return redirect('/note/' . $request->note_id . '/update?page=' . $page . '&filetab=1');
    	}
    	
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

	    if ($request->save_edit or $request->save_edit_hidden)
        	return redirect('/note/' . $note->id . '/update?page=' . $page . "&filetab=" . $request->filetab);
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
    	
    	//first we have to delelte all the files
    	$model_id = 'note,' . $note->id;
    	$entries = Fileentry::where('model_id', '=', $model_id)->get();
    	foreach ($entries as $entry) {
    		$file = Storage::disk('local')->delete($entry->filename);
    		$fname = $entry->original_filename;
    		$entry->delete();
    		Log::info('Deleted Files:' . $fname);
    	}

        $note->delete();
        
        $request->session()->flash('alert-success', 'Note was successful deleted!');
        $page = $request->session()->get('note_page');

        return redirect('/notes?page=' . $page);
    }
    
    
    
    public function upload(Request $request, Note $note)
    {
    	Log::info('Uploading Files!');
    
    	$file = $request->file;
    	$extension = $file->getClientOriginalExtension();
    	Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
    	$entry = new Fileentry();
    	$entry->mime = $file->getClientMimeType();
    	$entry->original_filename = $file->getClientOriginalName();
    	$entry->filename = $file->getFilename().'.'.$extension;
    	$entry->model_id = "note," . $note->id; 
    	$entry->save();
    	 
    	return ['success' => false, 'data' => 200];
    }

    
}
