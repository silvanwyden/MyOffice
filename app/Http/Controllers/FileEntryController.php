<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Fileentry;
use App\User;
use DB;
use Illuminate\Support\Facades\Storage;
use App\Repositories\FileEntryRepository;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Log;

class FileEntryController extends Controller
{
	
	
	/**
	 * The Warranty repository instance.
	 *
	 * @var WarrantyRepository
	 */
	protected $fileentries;
	
	/**
	 * Create a new controller instance.
	 *
	 * @param  WarrantyRepository  $fileentries
	 * @return void
	 */
	public function __construct(FileEntryRepository $fileentries)
	{
		$this->middleware('auth');
	
		$this->FileEntries = $fileentries;
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
				 
		//base query
		$fileentries = DB::table('fileentries')
		->select(
				'fileentries.original_filename',
				'fileentries.id',
				'fileentries.created_at',
				'fileentries.updated_at',
				'fileentries.model_id',
				'fileentries.mime'
		);
		 
		
		//handle search
		if ($request->btn_search == "s") {
			if ($request->search_text)
				$request->session()->put('fileentry_search_text', $request->search_text);
			else
				$request->session()->forget('fileentry_search_text');
		}
		$search_text = $request->session()->get('fileentry_search_text');
		if (strlen($search_text) > 0) {
			$fileentries->where(function($query) use ($search_text)
			{
				$query->where('fileentries.original_filename', 'like', "%" . $search_text . "%");
			});
		}
		 
		//handle sort order
		if ($request->order)
			$request->session()->put('fileentry_order', $request->order);
		$order = $request->session()->get('fileentry_order');
		if (!$order)
			$order = 'original_filename';
		 
		//handle sort direction
		if ($request->dir)
			$request->session()->put('fileentry_dir', $request->dir);
		$dir = $request->session()->get('fileentry_dir');
		if (!$dir)
			$dir = 'ASC';
		 
		//handle pagination -> we don't want to lose the page
		if ($request->page)
			$request->session()->put('fileentry_page', $request->page);
		$page = $request->session()->get('fileentry_page');
		 
		if ($request->n)
			$request->session()->put('pagination_number', $request->n);
		elseif ($request->session()->get('pagination_number') < 1)
		$request->session()->put('pagination_number', 100);
		$pagination_number = $request->session()->get('pagination_number');
		 
		$fileentries = $fileentries->orderBy('fileentries.'. $order, $dir)->paginate($pagination_number);
		 
		return view('fileentries.index', [
				'fileentries' => $fileentries,
				'order' => $order,
				'dir' => $dir,
				'page' => $page,
				'search_text' => $search_text,
				]);
	
	}
	
	
    
	public function get($fileid){
	
		$entry = Fileentry::where('id', '=', $fileid)->firstOrFail();
		//$file = Storage::disk('local')->get($entry->filename);
		
		$pathToFile=storage_path()."/app/".$entry->filename;
		return response()->download($pathToFile, $entry->original_filename);
	
		////->header('Content-Type', $entry->mime, 'Filename', 'test');
	}
	
	public function open($fileid){
 
 		$entry = Fileentry::where('id', '=', $fileid)->firstOrFail();
 		$file = Storage::disk('local')->get($entry->filename);
 	
 		return (new Response($file, 200, [
 				'Content-Type' => $entry->mime,
 				'Content-Disposition' => 'inline; filename="'.$entry->original_filename.'"',
 				]));
 	}
	
	public function destroy(Request $request, Fileentry $fileentry)
	{

		$entry = Fileentry::where('id', '=', $fileentry->id)->firstOrFail();
		$model = explode(",", $entry->model_id);
		$file = Storage::disk('local')->delete($entry->filename);
		$fname = $entry->original_filename;
		$fileentry->delete();
		
		Log::info('Deleted Files:' . $fname);
		
		$request->session()->flash('alert-success', 'File successful deleted!');
		
		if ($request->redirect == 'fileentries')
			return redirect('/fileentries?page=' . $request->page);
		else
			return redirect('/' . $model[0] . '/' . $model[1] . '/update?page=' . $request->page . '&filetab=1');
		
	}
	
}
