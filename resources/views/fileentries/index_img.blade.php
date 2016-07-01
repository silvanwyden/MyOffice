@extends('layouts.app')

@section('content')
	<script src="/dropzone.js"></script>
	<link rel="stylesheet" href="/dropzone.css">
	<div class="container">

		<div class="flash-message">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
		      @if(Session::has('alert-' . $msg))
		
		      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		      @endif
		    @endforeach
	  	</div> <!-- end .flash-message -->
		
		<div class="row">
			<div class="col-sm-8" style="padding-bottom: 6px;">
			  <div class="btn-group" role="group" aria-label="first">
			  	
			  	<a href="/fileentries?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
			  	<a href="/fileentries_img?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-picture"></span></a>
			  	
			  </div>
			</div>
			
			<form action="{{ url('fileentries') }}" method="GET" class="form-horizontal" id="form-search">
           			 {!! csrf_field() !!}
				
				<div class="col-sm-4" style="padding-bottom: 6px;">
					<input type="text" name="search_text" id="search-text" class="form-control" placeholder="Search" value="{{ $search_text or '' }}">
           		</div>
        		
        		<input type="hidden" name="btn_search" id="search" value="s">
           		
			</form>
			
		</div>
		<div id="unseen-fileentries">
					
			<div class="col-sm-12">
				<form action="/fileentry" method="POST" class="form-horizontal" id="myform">
				
							{{ csrf_field() }}
							
					<input type="hidden" name="rename_file_id" id="rename_file_id" value=""/>
				
						<div class="row">
						  	<div class="col-xs-6 col-md-3">

								@foreach ($fileentries as $fileentry)
									<a href="#" class="thumbnail">
										<img src="/fileentry/open_thumb/{{ $fileentry->id }}"/>
									</a>
								@endforeach
							
							</div>
						</div>
	
				</form>
				{!! $fileentries->appends([])->render() !!}
				
				<div id="dropzone" style="padding: 10px">
				  	<form action="/fileentries/upload" class="dropzone needsclick" id="demo-upload">
				  		{{ csrf_field() }}
		      			<div class="dz-message needsclick">Drop files here or click to upload.</div>
				    </form>
				</div>
				
			</div>
			
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search-text').focus();
		});

		shortcut.add("Ctrl+d",function() { window.location = "/fileentries/?search=&btn_search=s"; });

		function Rename(file_id) {
			$('#file_' + file_id).replaceWith("<input id='rename_file' name='rename_file' type='text' value='" + $('#file_' + file_id).text() + "' class='form-control' />");
			$('#file_download' + file_id).replaceWith("<button type='submit' class='btn btn-primary' style='margin-bottom: 5px;'><i class='glyphicon glyphicon-floppy-save'></i> Rename&nbsp;</button>");
			$('#file_rename' + file_id).replaceWith("<a href='' class='btn btn-warning' style='margin-bottom: 5px;'><i class='glyphicon glyphicon-minus'></i> Cancel</a>");
			$('#file_delete' + file_id).hide();
			$('#save-edit-hidden').val('save_edit_rename_filename');
			$('#rename_file_id').val(file_id);
			$('#rename_file').focus();
			
		}

	</script>
	
@endsection
