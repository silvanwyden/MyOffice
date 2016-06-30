<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Fileentry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;
use Log;

class FileEntryController extends Controller
{
    
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
		
		return redirect('/' . $model[0] . '/' . $model[1] . '/update?page=' . $request->page . '&filetab=1');
		
	}
	
}
