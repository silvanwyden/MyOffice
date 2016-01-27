@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					New Task
				</div>

				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')

					<!-- New Task Form -->
					<form action="/task" method="POST" class="form-horizontal">
						{{ csrf_field() }}

						<!-- Task Name -->
						<div class="form-group">
							<label for="task-name" class="col-sm-3 control-label">Task</label>

							<div class="col-sm-6">
								<input type="text" name="name" id="task-name" class="form-control" value="{{ old('name') }}">
							</div>
						</div>
						
						<!-- Category -->
						<div class="form-group">
							<label for="task-category" class="col-sm-3 control-label">Category</label>
							<div class="col-sm-6">
								<div class="dropdown">
								  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
								     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
								  </button>
								  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
								  	@foreach ($categories as $category)
									    <li><a href="#" ref="{{ $category->id }}">{{ $category->name }}</a></li>
								    @endforeach
								  </ul>
								</div>
							</div>
							<input type="hidden" id="category" name="category" value="0">
						</div>
						
						<!-- Deadline -->
						<div class="form-group">
							<label for="task-name" class="col-sm-3 control-label">Deadline</label>

							<div class="col-sm-6">
								<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ old('deadline') }}">
							</div>
						</div>
						
						<!-- Description -->
						<div class="form-group">
							<label for="task-description" class="col-sm-3 control-label">Description</label>

							<div class="col-sm-6">
								<textarea name="description" id="task-description" class="form-control" rows="10">{{ old('description') }}</textarea>
							</div>
						</div>

						<!-- Add Task Button -->
						<div class="form-group">
							<div class="col-sm-offset-3 col-sm-6">
								<button type="submit" class="btn btn-default">
									<i class="fa fa-btn fa-plus"></i>Add Task
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
	
@endsection
