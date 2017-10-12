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
	  	
		<form action="/task" method="POST" class="form-horizontal" id="myform">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/tasks?page={{ $page }}#task_{{ $task->id }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
						<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			
					</div>
				</div>
			</div>
		
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
					
						<div class="col-sm-10">
							Task
						</div>
					
						<div class="col-sm-2" style="text-align: right;">
					
							@if ($counter > 0)
								{{ $counter }}/{{ $total }} &nbsp;
						
								@if ($previous_id > 0)
									<a href="/task/{{ $previous_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-left"></a>
								@endif
								@if ($next_id > 0)
									<a href="/task/{{ $next_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-right"></a>
								@endif
							@endif
							
						</div>
						
					</div>
				</div>
				
				<div class="heading" style="padding:10px;">
					<ul class="nav nav-tabs">
				  		@if ($filetab)
				  			<li>
			  			@else
			  				<li class="active">
			  			@endif
				  			<a data-toggle="tab" href="#general" onclick="javascript:$('#filetab').val(0);">General</a></li>
				  			
					  	@if ($filetab)
				  			<li class="active">
			  			@else
			  				<li>
			  			@endif
			  				<a data-toggle="tab" href="#files" onclick="javascript:$('#filetab').val(1);">Files <span class="badge">{{ count($task->getFiles()) }}</span></a></li>
					</ul>
				</div>
				
				<div class="tab-content">
					@if ($filetab)
				 		<div id="general" class="tab-pane fade">
			 		@else
			 			<div id="general" class="tab-pane fade in active">
			 		@endif

						<div class="panel-body">
							<!-- Display Validation Errors -->
							@include('common.errors')
			
							<!-- if we are updating a task we need to know the task ID -->
							<input type="hidden" name="task_id" value="{{ $task->id or '' }}" />
							
							<!--  to save in javascript which was the opened tab -->
							<input type="hidden" id="filetab" name="filetab" value="{{ $filetab or '' }}" />
								
							<!-- Task Name -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Task</label>
			
								<div class="col-sm-10">
									<input type="text" name="name" id="task-name" class="form-control" value="{{ $task->name or old('name') }}">
								</div>
							</div>
							
							<!-- Category -->
							<div class="form-group">
								<label for="task-category" class="col-sm-2 control-label">Category</label>
								<div class="col-sm-10">
									<div class="dropdown-category">
									  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu" id="dropdown-category" aria-labelledby="dropdownMenu1">
									  	@foreach ($categories as $category)
										    <li js_id="category_{{ $category->id }}"><a href="#" ref="{{ $category->id }}">{{ $category->name }}</a></li>
									    @endforeach
									  </ul>
									</div>
								</div>
								<input type="hidden" id="category" name="category" value="0">
							</div>
							
							<!-- Priority -->
							<div class="form-group">
								<label for="task-priority" class="col-sm-2 control-label">Priority</label>
								<div class="col-sm-10">
									<div class="dropdown-priority">
									  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									     <span class="selection">Highest</span>&nbsp;&nbsp;<span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
									  	@foreach ($priorities as $priority)
										    <li js_id="priority_{{ $priority->id }}"><a href="#" ref="{{ $priority->id }}">{{ $priority->name }}</a></li>
									    @endforeach
									  </ul>
									</div>
								</div>
								<input type="hidden" id="priority" name="priority" value="1">
							</div>
							
							<!-- Stage -->
							<div class="form-group">
								<label for="task-priority" class="col-sm-2 control-label">Stage</label>
								<div class="col-sm-10">
									<div class="dropdown-stage">
									  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu3" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
									     <span class="selection">Open</span>&nbsp;&nbsp;<span class="caret"></span>
									  </button>
									  <ul class="dropdown-menu" aria-labelledby="dropdownMenu3">
									  	@foreach ($stages as $stage)
										    <li js_id="stage_{{ $stage->id }}"><a href="#" ref="{{ $stage->id }}">{{ $stage->name }}</a></li>
									    @endforeach
									  </ul>
									</div>
								</div>
								<input type="hidden" id="stage" name="stage" value="1">
							</div>
							
							<script>
			
								//function to show the selected item in the dropdown
								$(".dropdown-category .dropdown-menu li a").click(function(){
									$(this).parents(".dropdown-category").find('.selection').text($(this).text());
									$(this).parents(".dropdown-category").find('.selection').val($(this).text());
									$('#category').val($(this).attr('ref'));
									$('#category_name').val($(this).text());
								});
			
								$(".dropdown-priority .dropdown-menu li a").click(function(){
									$(this).parents(".dropdown-priority").find('.selection').text($(this).text());
									$(this).parents(".dropdown-priority").find('.selection').val($(this).text());
									$('#priority').val($(this).attr('ref'));
								});
			
								$(".dropdown-stage .dropdown-menu li a").click(function(){
									$(this).parents(".dropdown-stage").find('.selection').text($(this).text());
									$(this).parents(".dropdown-stage").find('.selection').val($(this).text());
									$('#stage').val($(this).attr('ref'));
								});
			
								//function to load the saved values
								$old_category="{{ old('category') }}";
								if (!$old_category) {
									$old_category ="{{ $category_id }}";
										if (!$old_category) {
											$old_category ="{{ $task->category_id }}";
										}
								}
								if ($old_category > 0) {
									$('#category').val($old_category);
									$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
									$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
									$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
								};
			
								$old_priority="{{ $task->priority_id or old('priority') }}";
								if ($old_priority > 0) {
									$('#priority').val($old_priority);
									$old_priority_name=$("li[js_id='priority_"+$old_priority+"']").text();
									$(".dropdown-priority .dropdown-menu li a").parents(".dropdown-priority").find('.selection').text($old_priority_name);
									$(".dropdown-priority .dropdown-menu li a").parents(".dropdown-priority").find('.selection').val($old_priority_name);
								};
			
								$old_stage="{{ $task->stage_id or old('stage') }}";
								if ($old_stage > 0) {
									$('#stage').val($old_stage);
									$old_stage_name=$("li[js_id='stage_"+$old_stage+"']").text();
									$(".dropdown-stage .dropdown-menu li a").parents(".dropdown-stage").find('.selection').text($old_stage_name);
									$(".dropdown-stage .dropdown-menu li a").parents(".dropdown-stage").find('.selection').val($old_stage_name);
								};
								
							</script>
							
							<!-- Deadline -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Deadline</label>
			
								<div class="col-sm-10">
									@if ($task->id)
										@if ($task->deadline > 0)
											<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($task->deadline)) }}">
										@else
											<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ old('deadline') }}">
										@endif
									@else
										<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime('now')) }}">
									@endif
								</div>
							</div>
							
							<!-- Create/Write -->
							@if ($task->id)
							<div class="form-group">
								<label for="task-created" class="col-sm-2 control-label">Create/Write</label>
			
								<div class="col-sm-10" style="padding-top: 7px;">{{ date('d.m.Y G:i:s', strtotime($task->created_at)) }} | {{ date('d.m.Y G:i:s', strtotime($task->updated_at)) }}</div>
			
							</div>
							@endif
							
							<!-- Description -->
							<div class="form-group">
								<label for="task-description" class="col-sm-2 control-label">Description</label>
			
								<div class="col-sm-10">
									<textarea name="description" id="summernote" class="form-control" >{{ $task->description or old('description') }}</textarea>
								</div>
							</div>
							
							<!-- Action Button -->
							<div class="form-group button-group">
								<div class="col-sm-offset-2 col-sm-9">
									<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
										<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
									</button>
									
									<input type="hidden" name="save_edit_hidden" id="save-edit-hidden" value=""/>
									<input type="hidden" name="rename_file_id" id="rename_file_id" value=""/>
																		
									<button type="submit" name="save_edit" class="btn btn-info" value="save_edit" style="margin-bottom: 5px;" id="save-edit>
										<i class="glyphicon glyphicon-floppy-saved"></i> Save&Edit&nbsp;
									</button>
									
									<a href="/tasks?page={{ $page }}" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
									
									@if ($task->id)
									<nobr>
										<a href="/task/{{ $task->id }}/done" class="btn btn-success" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-ok"></i> Done</a>
										<a href="/task/{{ $task->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
									</nobr>		
									@endif
									
								</div>
							</div>
						</div>
					</div>
				</form>
					
					@if ($filetab)
				 		<div id="files" class="tab-pane fade in active">
			 		@else
			 			<div id="files" class="tab-pane fade ">
			 		@endif
					    
					    <!--  show uploaded files -->
						<ul class="list-group" style="padding: 10px";>
							@foreach ($task->getFiles() as $file)
								<li class="list-group-item">
									<div class="form-inline input-append">
										<a id="file_{{ $file->id }}" href="/fileentry/open/{{ $file->id }}" target="_blank">{{ $file->original_filename}}</a>&nbsp;
										<a id="file_download{{ $file->id }}" href="/fileentry/get/{{ $file->id }}" class="btn btn-primary glyphicon glyphicon-download"></a>
										<a id="file_rename{{ $file->id }}"href="javascript:Rename({{ $file->id }});" class="btn btn-info glyphicon glyphicon-pencil"></a>
										<a id="file_delete{{ $file->id }}"href="/fileentry/delete/{{ $file->id }}?page={{ $page }}" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
									</div>
								</li>
							@endforeach
						</ul>
					    
					    <!-- File upload -->
					    @if ($task->id)
							<div id="dropzone" style="padding: 10px">
							  	<form action="/task/{{ $task->id }}/upload" class="dropzone needsclick" id="demo-upload">
							  		{{ csrf_field() }}
					      			<div class="dz-message needsclick">Drop files here or click to upload.</div>
							    </form>
							</div>
						@endif
									    
				  	</div>
				
				</div>
			</div>
		
		
		
		
		
		
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#task-name').focus();
		});

		shortcut.add("Ctrl+s",function() { $( "#myform" ).submit(); });
		shortcut.add("Ctrl+e",function() { $( "#save-edit-hidden" ).val('save_edit'); $( "#myform" ).submit(); });


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
