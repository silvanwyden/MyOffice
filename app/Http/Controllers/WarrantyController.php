<?php

namespace App\Http\Controllers;

use App\Warranty;
use App\Http\Requests;
use App\User;
use App\Category;
use App\Tag;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\WarrantyRepository;
use DateTime;
use App\Session;
use DB;
use Log;
use Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use App\Fileentry;

class WarrantyController extends Controller
{
    /**
     * The Warranty repository instance.
     *
     * @var WarrantyRepository
     */
    protected $warranties;

    /**
     * Create a new controller instance.
     *
     * @param  WarrantyRepository  $warranties
     * @return void
     */
    public function __construct(WarrantyRepository $warranties)
    {
        $this->middleware('auth');

        $this->Warranties = $warranties;
    }

    /**
     * Display a list of all of the user's Warranty.
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
	    		$request->session()->put('warranty_category_id', $request->category_id);
	    		$request->session()->put('warranty_category',Category::find($request->category_id)->name);
	    	}
	    	else {
	    		$request->session()->put('warranty_category_id', False);
	    		$request->session()->put('warranty_category', "All Categories");
    	}
    	$ses_category_id = $request->session()->get('warranty_category_id');
    	
    	if ($ses_category_id)
    		$tags = Tag::where('category_id', '=', $ses_category_id)->orderBy('name')->get();
    	else
    		$tags = Tag::all()->sortBy('name');
    	
    	//base query
    	$warranties = DB::table('warranties')
    		->leftjoin('categories', 'warranties.category_id', '=', 'categories.id')
    		->select(
    				'warranties.title', 
    				'warranties.id',
    				'warranties.created_at',
    				'warranties.updated_at', 
    				'warranties.tag_ids',
    				'categories.name as cname', 
    				'categories.css_class',
    				'warranties.date_purchase',
    				'warranties.warranty_months',
    				'warranties.date_warranty',
    				'warranties.location'
    				);
    	
    	//handle categories
    	if ($ses_category_id)
    		$warranties->where('category_id', '=', $ses_category_id);
    	
    	//handle search tags
    	$tags_sel = array();
    	if ($request->btn_search == "s") {
    		if ($request->search)
    			$request->session()->put('warranty_search', $request->search);
    		else
    			$request->session()->forget('warranty_search');
    	}
    	$search = $request->session()->get('warranty_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$warranties->where('tag_ids', 'like', "%," . $s . ",%");
    	}
    	
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search_text) 
    			$request->session()->put('warranty_search_text', $request->search_text);
    		else
    			$request->session()->forget('warranty_search_text');
    	}
    	$search_text = $request->session()->get('warranty_search_text');
    	if (strlen($search_text) > 0) {
    		$warranties->where(function($query) use ($search_text)
    		{
    			$query->where('warranties.title', 'like', "%" . $search_text . "%");
    		});
    	}
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('warranty_order', $request->order);
    	$order = $request->session()->get('warranty_order');
    	if (!$order)
    		$order = 'date_warranty';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('warranty_dir', $request->dir);
    	$dir = $request->session()->get('warranty_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('warranty_page', $request->page);
    	$page = $request->session()->get('warranty_page');
    	
    	if ($request->n)
    		$request->session()->put('pagination_number', $request->n);
    	elseif ($request->session()->get('pagination_number') < 1)
    	$request->session()->put('pagination_number', 100);
    	$pagination_number = $request->session()->get('pagination_number');
    	
    	$warranties = $warranties->orderBy('warranties.'. $order, $dir)->orderBy('warranties.title', 'ASC')->paginate($pagination_number);
    	
        return view('warranties.index', [
        	'categories' => $categories,
            'warranties' => $warranties,
        	'order' => $order,
        	'dir' => $dir,
        	'category' => $request->session()->get('warranty_category'),
        	'category_id' => $request->session()->get('warranty_category_id'),
        	'search' => $search,
        	'page' => $page,
        	'tags' => $tags,
        	'tags_sel' => $tags_sel,
        	'search_text' => $search_text,
        ]);
        
    }

    
    /**
     * Create a new Warranty: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	
    	$user = User::find($request->user()->id);
    	$categories = Category::All(['id', 'name']);
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	
    	return view('warranties.update', [
    			'categories' => $categories,
    			'category_id' => $request->session()->get('warranty_category_id'),
    			'counter' => 0,
    			'tags' => $tags,
    			'tags_sel' => array(),
    			'page' => $request->session()->get('warranty_page'),
    			'filetab' => 0,
    			])->withWarranty(new Warranty());
    	 
    }
    
    
    /**
     * Update a new Warranty: load date and forward to view
     *
     * @param  Request  $request, Warranty $warranty
     * @return view
     */
    public function update(Request $request, Warranty $warranty) {
    
    	$categories = Category::All(['id', 'name']);
    	$tags = Tag::All(['id', 'name', 'css_class']);
    	$user = User::find($request->user()->id);
    	
    	//get next id
    	$warranties = DB::table('warranties')
    	->leftjoin('categories', 'warranties.category_id', '=', 'categories.id')
    	->select('warranties.id as id');
    	    	 
    	//handle categories
    	$ses_category_id = $request->session()->get('warranty_category_id');
    	if ($ses_category_id)
    		$warranties->where('category_id', '=', $ses_category_id);

    	//handle search tags
    	$search = $request->session()->get('warranty_search');
    	if (strlen($search) > 0) {
    		$search_array = explode(",", $search);
    		$tags_sel = Tag::find($search_array);
    		foreach(explode(",", $search) as $s)
    			$warranties->where('tag_ids', 'like', "%," . $s . ",%");
    	}
    	 
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search_text)
    			$request->session()->put('warranty_search_text', $request->search_text);
    		else
    			$request->session()->forget('warranty_search_text');
    	}
    	$search_text = $request->session()->get('warranty_search_text');
    	if (strlen($search_text) > 0) {
    		$warranties->where(function($query) use ($search_text)
    		{
    			$query->where('warranties.title', 'like', "%" . $search_text . "%");
    		});
    	}

    	//handle sort order
    	$order = $request->session()->get('warranty_order');
    	if (!$order)
    		$order = 'title';
    	 
    	//handle sort direction
    	$dir = $request->session()->get('warranty_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	$pagination_number = $request->session()->get('pagination_number');
    	
    	$warranties = $warranties->orderBy('warranties.'. $order, $dir)->orderBy('warranties.title', 'ASC')->paginate($pagination_number);
    	
    	$previous_id = 0;
    	$next_id = 0;
    	$counter = 0;
    	$found = false;
    	foreach ($warranties as $temp) {
    		
    		if ($found) {
    			$next_id = $temp->id;
    			break;
    		}
    		
    		if ($temp->id == $warranty->id) {
    			$found = true;
    		}
    		
    		$counter++;
    		if (!$found)
    			$previous_id = $temp->id;
    		
    	}

    	return view('warranties.update', [
    			'categories' => $categories,
    			'warranty' => $warranty,
    			'category_id' => False,
    			'tags' => $tags,
    			'tags_sel' => $tags_sel = Tag::find(explode(",", $warranty->tag_ids)),
    			'previous_id' => $previous_id,
    			'next_id' => $next_id,
    			'counter' => $counter,
    			'total' => count($warranties),
    			'page' => $request->session()->get('warranty_page'),
    			'filetab' => $request->filetab,
    			])->withWarranty($warranty);
    }

    /**
     * Validate AND Save/Crate a new Warranty.
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
        	
        $date_purchase = False;
        if ($request->date_purchase) {
        	$date_purchase = DateTime::createFromFormat('d.m.Y', $request->date_purchase);
        	$date_purchase = $date_purchase->format('Y-m-d');
        	$date_warranty = DateTime::createFromFormat('d.m.Y', $request->date_purchase)->modify('+' . $request->warranty_months . ' month');
        }
        
        $input = array(
	            'title' => $request->title,
	        	'category_id' => $request->category,
        		'tag_ids' => ',' . $request->tags . ',',
        		'warranty_months' => $request->warranty_months,
        		'location' => $request->location,
        		'date_purchase' => $date_purchase,
        		'date_warranty' => $date_warranty,
	        );       
        
        if ($request->warranty_id) {
        	
        	$warranty = Warranty::find($request->warranty_id);
        	$warranty->fill($input)->save();
        	$request->session()->flash('alert-success', 'Warranty was successful updated!');
        	
        	}
        else {
        	
	        $warranty = new Warranty();
    		$warranty = $warranty->create($input);
	        $request->session()->flash('alert-success', 'Warranty was successful added!');
	        
	        }

	    $page = $request->session()->get('warranty_page');

	    if ($request->save_edit or $request->save_edit_hidden)
        	return redirect('/warranty/' . $warranty->id . '/update?page=' . $page);
	    else
        	return redirect('/warranties?page=' . $page);
    }
    
    

    /**
     * Destroy the given Warranty.
     *
     * @param  Request  $request
     * @param  Warranty  $warranty
     * @return Response
     */
    public function destroy(Request $request, Warranty $warranty)
    {
    	
    	//first we have to delelte all the files
    	$model_id = 'warranty,' . $warranty->id;
    	$entries = Fileentry::where('model_id', '=', $model_id)->get();
    	foreach ($entries as $entry) {
    		$file = Storage::disk('local')->delete($entry->filename);
    		$fname = $entry->original_filename;
    		$entry->delete();
    		Log::info('Deleted Files:' . $fname);
    	}

        $warranty->delete();
        
        $request->session()->flash('alert-success', 'Warranty was successful deleted!');
        $page = $request->session()->get('warranty_page');

        return redirect('/warranties?page=' . $page);
    }
    
    
    
    public function upload(Request $request, Warranty $warranty)
    {
    	Log::info('Uploading Files!');
    
    	$file = $request->file;
    	$extension = $file->getClientOriginalExtension();
    	Storage::disk('local')->put($file->getFilename().'.'.$extension,  File::get($file));
    	$entry = new Fileentry();
    	$entry->mime = $file->getClientMimeType();
    	$entry->original_filename = $file->getClientOriginalName();
    	$entry->filename = $file->getFilename().'.'.$extension;
    	$entry->model_id = "warranty," . $warranty->id;
    	 
    	$entry->save();
    	 
    	return ['success' => false, 'data' => 200];
    }

    
}
