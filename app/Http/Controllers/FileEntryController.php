<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Fileentry;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Response;

class FileEntryController extends Controller
{
    
	public function get($fileid){
	
		$entry = Fileentry::where('id', '=', $fileid)->firstOrFail();
		$file = Storage::disk('local')->get($entry->filename);
	
		return (new Response($file, 200))
		->header('Content-Type', $entry->mime);
	}
	
}
