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
	
		<form action="/countercategory" method="POST" class="form-horizontal">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/countercategories?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			
					</div>
				</div>
			</div>
	
			<div class="panel panel-default">
				<div class="panel-heading">
					Counter Category
				</div>
	
				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')

					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="countercategory_id" value="{{ $countercategory->id or '' }}" />

					<!-- Task Name -->
					<div class="form-group">
						<label for="countercategory-name" class="col-sm-2 control-label">Name</label>
	
						<div class="col-sm-10">
							<input type="text" name="name" id="countercategory-name" class="form-control" value="{{ $countercategory->name or old('name') }}">
						</div>
					</div>
					
					<!-- Counter Category CSS Class -->
					<div class="form-group">
						<label for="countercategory-css-class" class="col-sm-2 control-label">Name</label>
	
						<div class="col-sm-10">
							<input type="text" name="css_class" id="countercategory-css-class" class="form-control" value="{{ $countercategory->css_class or old('css_class') }}">
						</div>
					</div>
					
					<!-- Task inactive -->
					<div class="form-group">
						<label for="countercategory-name" class="col-sm-2 control-label">inactive</label>
	
						<div class="col-sm-10">
							<div class="checkbox">
	  							<label>
									@if ($countercategory->inactive)
										<input type="checkbox" name="inactive" id="countercategory-inactive"  value="1" checked>
									@else
										<input type="checkbox" name="inactive" id="countercategory-inactive"  value="1">
									@endif
								</label>
							</div>
						</div>
					</div>

					
					<!-- Action Button -->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
							</button>
							
							<button type="submit" name="save_edit" class="btn btn-info" value="save_edit" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-saved"></i> Save&Edit&nbsp;
							</button>
							
							<a href="/countercategories?page={{ $page }}" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($countercategory->id)
							<nobr>
								<a href="/countercategory/{{ $countercategory->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
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
			$('#countercategory-name').focus();
		});

	</script>
	
@endsection
