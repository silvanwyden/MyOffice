@extends('layouts.app')

@section('content')
	<div class="container">

		<div class="flash-message">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
		      @if(Session::has('alert-' . $msg))
		
		      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		      @endif
		    @endforeach
	  	</div> <!-- end .flash-message -->
		
		<div class="row">
			<div class="col-sm-6" style="padding-bottom: 6px;">
			  
			  About
			  
			  <p>
			  
			  Shortcuts:<br/>
			  <ul>
			  	<li>Ctrl + m: new entry</li>
			  	<li>Ctrl + d: reset search</li>
			  	<li>Ctrl + s: save entry</li>
			  	<li>Ctrl + e: save & edit entry</li>
			  	<li>Ctrl + i: toggle between critical tasks</li>
			  </ul>
			  
			  </p>
			  
			</div>
	</div>
	
@endsection
