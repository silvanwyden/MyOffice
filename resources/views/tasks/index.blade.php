@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<!-- Current Tasks -->
			
			<a href="/task">New Task</a>
			
			@if (count($tasks) > 0)
				<div class="panel panel-default">
					<div class="panel-heading">
						Current Tasks
					</div>

					<div class="panel-body">
						<table class="table table-striped task-table" id="clickable">
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
										<td class="table-text">
											<a href="/task/{{ $task->id }}/update">
												<div>{{ $task->name }}</div>
											</a>
										</td>
										<!-- td class="table-text"><div>{{ date('d.m.Y H:i', strtotime($task->created_at)) }}</div></td-->
										<!-- td class="table-text"><div>{{ date('d.m.Y H:i', strtotime($task->modiefied_at)) }}</div></td-->
										<td class="table-text"><div>{{ date('d.m.Y', strtotime($task->deadline)) }}</div></td>

										<!-- Task Delete Button -->
										<td>
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
			@endif
		</div>
	</div>
@endsection
