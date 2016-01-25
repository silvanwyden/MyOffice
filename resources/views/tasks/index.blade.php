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

			<!-- Current Tasks -->
			@if (count($tasks) > 0)
				<div class="panel panel-default">
					<div class="panel-heading">
						Current Tasks
					</div>

					<div class="panel-body">
						<table class="table table-striped task-table">
							<thead>
								<!-- th>ID</th-->
								<th>Task</th>
								<!-- th>Created</th-->
								<!-- th>Modified</th-->
								<th>Deadline</th>
								<th>Action</th>
							</thead>
							<tbody>
								@foreach ($tasks as $task)
									<tr>
										<!-- td class="table-text"><div>{{ $task->id }}</div></td-->
										<td class="table-text"><div>{{ $task->name }}</div></td>
										<!-- td class="table-text"><div>{{ date('d.m.Y H:i', strtotime($task->created_at)) }}</div></td-->
										<!-- td class="table-text"><div>{{ date('d.m.Y H:i', strtotime($task->modiefied_at)) }}</div></td-->
										<td class="table-text"><div>{{ $task->deadline }}</div></td>

										<!-- Task Delete Button -->
										<td>
											<a href="/task/{{ $task->id }}/update">
												
												
													<i class="fa fa-btn fa-edit"></i>Edit
													
											</a>
											<form action="/task/{{ $task->id }}" method="POST">
												{{ csrf_field() }}
												{{ method_field('DELETE') }}

												<button type="submit" id="delete-task-{{ $task->id }}" class="btn btn-danger">
													<i class="fa fa-btn fa-trash"></i>Delete
												</button>
											</form>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $tasks->appends(['sort' => 'name'])->render() !!}
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
