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
			  <div class="btn-group" role="group" aria-label="first">
			  	&nbsp;
			  </div>
			</div>
			
			<form action="{{ url('fileentries') }}" method="GET" class="form-horizontal" id="form-search">
           			 {!! csrf_field() !!}
				
				<div class="col-sm-3" style="padding-bottom: 6px;">
					<input type="text" name="search_text" id="search-text" class="form-control" placeholder="Search" value="{{ $search_text or '' }}">
           		</div>
        		
        		<input type="hidden" name="btn_search" id="search" value="s">
           		
			</form>
			
		</div>
		<div id="unseen-fileentries">
					
			<div class="col-sm-10">
		
				<table class="table table-striped fileentry-table">
					<thead>
					<tr>
						<th><nobr><a href="{{ createOrderLink('original_filename', $order, $dir, $page) }}">Name</a> <div class="{{ createOrderLinkImage('original_filename', $order, $dir) }}"></div></nobr></th>
						<th><nobr><a href="{{ createOrderLink('mime', $order, $dir, $page) }}">Type</a> <div class="{{ createOrderLinkImage('mime', $order, $dir) }}"></div></nobr></th>
						<th><nobr><a href="{{ createOrderLink('model_id', $order, $dir, $page) }}">Model</a> <div class="{{ createOrderLinkImage('model_id', $order, $dir) }}"></div></nobr></th>
						<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($fileentries as $fileentry)
							<tr>
								<!-- td class="table-text"><div>{{ $fileentry->id }}</div></td-->
								<td class="table-text">
									<a id="file_{{ $fileentry->id }}" href="/fileentry/open/{{ $fileentry->id }}" target="_blank">{{ $fileentry->original_filename}}</a>&nbsp;
								</td>
								<td class="table-text">{{ explode("/", $fileentry->mime)[1] }}</td>
								<td class="table-text">
									<a href="/{{ explode(",", $fileentry->model_id)[0] }}/{{ explode(",", $fileentry->model_id)[1] }}/update">{{ ucfirst(explode(",", $fileentry->model_id)[0]) }}</a>
								</td>
															
								<!-- fileentry Action Buttons -->
								<td>
									<nobr>
										
										<a id="file_download{{ $fileentry->id }}" href="/fileentry/get/{{ $fileentry->id }}" class="btn btn-primary glyphicon glyphicon-download"></a>
										<a id="file_rename{{ $fileentry->id }}"href="javascript:Rename({{ $fileentry->id }});" class="btn btn-info glyphicon glyphicon-pencil"></a>
										<a id="file_delete{{ $fileentry->id }}"href="/fileentry/delete/{{ $fileentry->id }}?page={{ $page }}&redirect=fileentries" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
									</nobr>				
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{!! $fileentries->appends([])->render() !!}
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
