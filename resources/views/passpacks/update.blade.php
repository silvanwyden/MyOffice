@extends('layouts.app')

@section('content')
	<div class="container">
	
		<form action="/passpack" method="POST" class="form-horizontal">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/passpacks" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		
					</div>
				</div>
			</div>
		
			<div class="panel panel-default">
				<div class="panel-heading">
					New PassPack
				</div>
	
				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')
	
					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="passpack_id" value="{{ $passpack->id or '' }}" />
	
					<!-- Passpack URL -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">URL</label>
	
						<div class="col-sm-10">
							<input type="text" name="url" id="passpack-url" class="form-control" value="{{ $passpack->url or old('url') }}">
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
					
					
					<script>
	
						//function to show the selected item in the dropdown
						$(".dropdown-category .dropdown-menu li a").click(function(){
							$(this).parents(".dropdown-category").find('.selection').text($(this).text());
							$(this).parents(".dropdown-category").find('.selection').val($(this).text());
							$('#category').val($(this).attr('ref'));
							$('#category_name').val($(this).text());
						});
	
					
						//function to load the saved values
						$old_category="{{ old('category') }}";
						if (!$old_category) {
							$old_category ="{{ $category_id }}";
								if (!$old_category) {
									$old_category ="{{ $passpack->category_id }}";
								}
						}
						if ($old_category > 0) {
							$('#category').val($old_category);
							$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
						};

						
					</script>
					

					
					<!-- Read/Write -->
					@if ($passpack->id)
					<div class="form-group">
						<label for="task-created" class="col-sm-2 control-label">Read/Write</label>
	
						<div class="col-sm-10" style="padding-top: 7px;">{{ date('d.m.Y G:i:s', strtotime($passpack->created_at)) }} | {{ date('d.m.Y G:i:s', strtotime($passpack->updated_at)) }}</div>
	
					</div>
					@endif
					
					<!-- Passpack User -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">User</label>
	
						<div class="col-sm-10">
							<input type="text" name="user" id="passpack-user" class="form-control" value="{{ $passpack->user or old('user') }}">
						</div>
					</div>
					
					<!-- Passpack Password -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Password</label>
	
						<div class="col-sm-10">
							<input type="text" name="password" id="passpack-password" class="form-control" value="{{ $pwd or old('password') }}">
						</div>
					</div>
					
					<!-- Action Button -->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
							</button>
							
							<a href="/passpacks" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($passpack->id)
							<nobr>
								<a href="/passpack/{{ $passpack->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
							</nobr>		
							@endif
							
						</div>
					</div>
						
				</div>
			</div>
		</form>
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#passpack-url').focus();
		});

	</script>
	
@endsection
