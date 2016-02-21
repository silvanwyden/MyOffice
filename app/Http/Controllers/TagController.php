<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Http\Requests;
use App\User;
use App\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\TagRepository;
use DateTime;
use App\Session;
use DB;

class TagController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $tags;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(TagRepository $tags)
    {
        $this->middleware('auth');

        $this->tags = $tags;
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
    	$categories = category::All(['id', 'name']);
    	
    	//handle categories filter
    	if ($request->category_id)
    		if ($request->category_id > 0) {
    		$request->session()->put('tag_category_id', $request->category_id);
    		$request->session()->put('tag_category', Category::find($request->category_id)->name);
    	}
    	else {
    		$request->session()->forget('tag_category_id');
    		$request->session()->put('tag_category', 'All Categories');
    	}
    	$ses_category_id = $request->session()->get('tag_category_id');
    	
    	//base query
    	$tags = DB::table('tags')
    	->leftjoin('categories', 'tags.category_id', '=', 'categories.id')
    	->select(
    			'tags.id',
    			'tags.name',
    			'tags.css_class',
    			'tags.seq',
    			'tags.category_id',
    			'categories.name as cname',
    			'categories.css_class as ccss_class'
    	);
    	
    	//handle categories
    	if ($ses_category_id)
    		$tags->where('category_id', '=', $ses_category_id);
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('tag_order', $request->order);
    	$order = $request->session()->get('tag_order');
    	if (!$order)
    		$order = 'name';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('tag_dir', $request->dir);
    	$dir = $request->session()->get('tag_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('tag_page', $request->page);
    	$page = $request->session()->get('tag_page');
    	
    	$tags = $tags->orderBy($order, $dir)->paginate(50);
    	
        return view('tags.index', [
        	'tags' => $tags,
        	'categories' => $categories,
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'category' => $request->session()->get('tag_category'),
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
    	
    	return view('tags.update', [
    			'categories' => $categories,
    			'category_id' => $request->session()->get('tag_category_id'),
    			])->withTag(new Tag());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Tag $tag) {
    	
    	$categories = Category::All(['id', 'name']);

    	return view('tags.update', [
    			'categories' => $categories,
				'tag' => $tag,
    			'category_id' => False,
    			])->withTag($tag);
    }
    
    
    /**
     * Validate AND Save/Crate a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    
    	$input = array(
    			'name' => $request->name,
    			'category_id' => $request->category,
    			'css_class' => $request->css_class,
    			'seq' => $request->seq,
    	);
    
    	if ($request->tag_id) {
    		 
    		$tag = Tag::find($request->tag_id);
    		$tag->fill($input)->save();
    		$request->session()->flash('alert-success', 'Tag was successful updated!');
    		 
    	}
    	else {
    		 
    		$tag = new Tag();
    		$tag = $tag->create($input);
    		$request->session()->flash('alert-success', 'Tag was successful added!');
    		 
    	}
    
    	$page = $request->session()->get('tag_page');
    	 
    	if ($request->save_edit)
    		return redirect('/tag/' . $tag->id . '/update');
    	else
    		return redirect('/tags?page=' . $page);
    }

    
    
    
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Tag $tag)
    {

    	$tag->delete();
    
    	$request->session()->flash('alert-success', 'Tag was successful deleted!');
    
    	return redirect('/tags');
    }
    

    
    
}
