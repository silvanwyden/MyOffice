<?php

namespace App\Http\Controllers;

use App\Passpack;
use App\Category;
use App\Http\Requests;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\PasspackRepository;
use App\Session;
use DB;
use Crypt;

class PasspackController extends Controller
{
    /**
     * The passpack repository instance.
     *
     * @var PasspackRepository
     */
    protected $passpacks;

    /**
     * Create a new controller instance.
     *
     * @param  PasspackRepository  $passpacks
     * @return void
     */
    public function __construct(PasspackRepository $passpacks)
    {
        $this->middleware('auth');

        $this->passpacks = $passpacks;
    }

    /**
     * Display a list of all of the user's passpack.
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
	    		$user->passpack_category_id = $request->category_id;
	    		$user->passpack_category = Category::find($request->category_id)->name;
	    		$user->save();
	    	}
	    	else {
	    		$user->passpack_category_id = False;
	    		$user->passpack_category = "All Categories";
	    		$user->save();
    	}
    	$ses_category_id = $user->passpack_category_id;
    	
    	//base query
    	$passpacks = DB::table('passpacks')
    		->leftjoin('categories', 'passpacks.category_id', '=', 'categories.id')
    		->select(
    				'passpacks.url', 
    				'passpacks.user', 
    				'passpacks.password',
    				'passpacks.id',
    				'passpacks.created_at',
    				'passpacks.updated_at', 
    				'categories.name as cname', 
    				'categories.css_class'
    				);
    	
    	//handle categories
    	if ($ses_category_id)
    		$passpacks->where('category_id', '=', $ses_category_id);
    	
    	//handle search
    	if ($request->btn_search == "s") {
    		if ($request->search) 
    			$request->session()->put('passpack_search', $request->search);
    		else
    			$request->session()->forget('passpack_search');
    	}
    	$search = $request->session()->get('passpack_search');
    	if (strlen($search) > 0)
    		$passpacks->where('passpacks.url', 'like', "%" . $search . "%");
    	
    	//handle sort order
    	if ($request->order)
    		$request->session()->put('passpack_order', $request->order);
    	$order = $request->session()->get('passpack_order');
    	if (!$order)
    		$order = 'url';
    	
    	//handle sort direction
    	if ($request->dir)
    		$request->session()->put('dir', $request->dir);
    	$dir = $request->session()->get('dir');
    	if (!$dir)
    		$dir = 'ASC';
    	
    	//handle pagination -> we don't want to lose the page
    	if ($request->page)
    		$request->session()->put('passpack_page', $request->page);
    	$page = $request->session()->get('passpack_page');
    	
    	$passpacks = $passpacks->orderBy($order, $dir)->paginate(50);
    	
        return view('passpacks.index', [
        	'categories' => $categories,
            'passpacks' => $passpacks,
        	'order' => $order,
        	'dir' => $dir,
        	'category' => $user->passpack_category,
        	'search' => $search,
        	'page' => $page,
        ]);
        
    }

    
    /**
     * Create a new passpack: load date and forward to view
     *
     * @param  Request  $request
     * @return view
     */
    public function create(Request $request) {
    	
    	$user = User::find($request->user()->id);
    	$categories = Category::All(['id', 'name']);
    	    	
    	return view('passpacks.update', [
    			'categories' => $categories,
    			'category_id' => $user->passpack_category_id,
    			'pwd' = '',
    			])->withPasspack(new Passpack());
    	 
    }
    
    
    /**
     * Update a new passpack: load date and forward to view
     *
     * @param  Request  $request, Passpack $passpack
     * @return view
     */
    public function update(Request $request, Passpack $passpack) {
    
    	$categories = Category::All(['id', 'name']);
    	
    	$password = Crypt::decrypt($passpack->password);
    
    	return view('passpacks.update', [
    			'categories' => $categories,
    			'passpack' => $passpack,
    			'pwd' => $password,
    			'category_id' => False,
    			])->withPasspack($passpack);
    }
    

    /**
     * Validate AND Save/Crate a new passpack.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'url' => 'required|max:255',
        ]);
        
        $input = array(
	            'url' => $request->url,
	        	'user' => $request->passpack_user,
	        	'password' => Crypt::encrypt($request->passpack_password),
	        	'category_id' => $request->category,
	        );
        
        if ($request->passpack_id) {
        	
        	$passpack = Passpack::find($request->passpack_id);
        	$passpack->fill($input)->save();
        	$request->session()->flash('alert-success', 'Passpack was successful updated!');
        	
        	}
        else {
        	$passpack = new Passpack();
	       	$passpack->create($input);
	        $request->session()->flash('alert-success', 'Passpack was successful added!');
	        
	        }

	    $page = $request->session()->get('page');
        	
	    if ($request->save_edit)
	    	return redirect('/passpack/' . $passpack->id . '/update');
    	else
    		return redirect('/passpacks?page=' . $page);
    		
    }
    
    

    /**
     * Destroy the given passpack.
     *
     * @param  Request  $request
     * @param  Passpack  $passpack
     * @return Response
     */
    public function destroy(Request $request, Passpack $passpack)
    {
        $passpack->delete();
        
        $request->session()->flash('alert-success', 'Passpack was successful deleted!');

        return redirect('/passpacks');
    }
    
    

}
