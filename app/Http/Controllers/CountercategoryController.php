<?php

namespace App\Http\Controllers;

use App\Tag;
use App\Http\Requests;
use App\User;
use App\Category;
use App\Countercategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\CountercategoryRepository;
use DateTime;
use App\Session;
use DB;
use Log;

class CountercategoryController extends Controller
{
    /**
     * The task repository instance.
     *
     * @var TaskRepository
     */
    protected $countercategories;

    /**
     * Create a new controller instance.
     *
     * @param  TaskRepository  $tasks
     * @return void
     */
    public function __construct(CountercategoryRepository $countercategories)
    {
        $this->middleware('auth');

        $this->tags = $countercategories;
    }

    /**
     * Display a list of all of the user's task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function index(Request $request)
    {
    	
    	//base query
    	$countercategories = DB::table('countercategories')
    	->select(
    			'countercategories.id',
    			'countercategories.name',
    			'countercategories.inactive'
    	);
    	    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('countercategories_order', $request->order);
    	$order = $request->session()->get('countercategories_order');
    	if (!$order)
    		$order = 'name';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('countercategories_dir', $request->dir);
    	$dir = $request->session()->get('countercategories_dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('countercategories_page', $request->page);
    	$page = $request->session()->get('countercategories_page');
    	
    	$countercategories = $countercategories->orderBy($order, $dir)->paginate(50);
    	
        return view('countercategories.index', [
        	'order' => $order,
        	'dir' => $dir,
        	'page' => $page,
        	'countercategories' => $countercategories,
        ]);
        
    }
    
    
    /**
     * Create a new task: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	 
    	return view('countercategories.update', [
    			'countercategory' => new Countercategory(),
    			])->withCountercategorys(new Countercategory());
    
    }
    
    
    /**
     * Update a new task: load date and forward to view
     *
     * @param  Request  $request, Task $task
     * @return view
     */
    public function update(Request $request, Countercategory $countercategory) {
    	
    	return view('countercategories.update', [
				'countercategory' => $countercategory,
    			])->withCountercategorys($countercategory);
    }
    
    
    /**
     * Validate AND Save/Crate a new task.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
    	$this->validate($request, [
    			'name' => 'required|max:255',
    			]);
    	
    	$inactive = 0;
    	if ($request->inactive)
    		$inactive = 1;
    	
    	$input = array(
    			'name' => $request->name,
    			'inactive' => $inactive,
    	);
    
    	if ($request->countercategory_id) {
    		 
    		$countercategory = Countercategory::find($request->countercategory_id);
    		$countercategory->fill($input)->save();
    		$request->session()->flash('alert-success', 'Counter Category was successful updated!');
    		 
    	}
    	else {
    		 
    		$countercategory = new Countercategory();
    		$countercategory = $countercategory->create($input);
    		$request->session()->flash('alert-success', 'Counter Category was successful added!');
    		 
    	}
    
    	$page = $request->session()->get('countercategory_page');
    	 
    if ($request->save_edit)
    		return redirect('/countercategory/' . $countercategory->id . '/update');
    	else
    		return redirect('/countercategories?page=' . $page);
    }

    
    
    
    /**
     * Destroy the given task.
     *
     * @param  Request  $request
     * @param  Task  $task
     * @return Response
     */
    public function destroy(Request $request, Countercategory $countercategory)
    {

    	$countercategory->delete();
    
    	$request->session()->flash('alert-success', 'Counter Category was successful deleted!');
    
    	return redirect('/countercategories');
    }
    


    
    
}
