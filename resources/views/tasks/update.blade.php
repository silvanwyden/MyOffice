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

					
					{!! Form::open(array('url' => 'foo/bar')) !!}
    //
{!! Form::close() !!}
					
					
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
						
						<!-- Deadline -->
						<div class="form-group">
							<label for="task-name" class="col-sm-3 control-label">Deadline</label>

							<div class="col-sm-6">
								<input type="text" name="deadline" id="datepicker" class="form-control" value="{{ old('deadline') }}">
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
