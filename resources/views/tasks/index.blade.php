@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
			<!-- Current Tasks -->
			
			<a href="/task" class="btn btn-primary">Create New Task</a><br><br>
			
			@if (count($tasks) > 0)
				<div class="panel panel-default">
					<div class="panel-heading">
						Current Tasks
					</div>

					<div class="panel-body">
						<table class="table table-striped task-table" id="clickable">
							<thead>
								<th><a href="{{ createOrderLink('name', $order, $dir) }}">Task</a></th>
								<th><a href="{{ createOrderLink('category_id', $order, $dir) }}">Category</a></th>
								<th><a href="{{ createOrderLink('priority_id', $order, $dir) }}">Priority</a></th>
								<th><a href="{{ createOrderLink('deadline', $order, $dir) }}">Deadline</a></th>
								<th><a href="{{ createOrderLink('stage_id', $order, $dir) }}">State</a></th>
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
										<td class="table-text"><div class="btn {{ $task->category['css_class'] }}">{{ $task->category['name'] }}</div></td>
										<td class="table-text"><div>{{ $task->priority['name'] }}</div></td>
										<td class="table-text">
											<div>
												@if ($task->deadline != '0000-00-00')
													{{ date('d.m.Y', strtotime($task->deadline)) }}
												@endif
											</div>
										</td>
										<td class="table-text"><div>{{ $task->stage['name'] }}</div></td>
										
										<!-- Task Delete Button -->
										<td>
											<form action="/task/{{ $task->id }}" method="POST">
												{{ csrf_field() }}
												{{ method_field('DELETE') }}

												<button type="submit" id="delete-task-{{ $task->id }}" class="btn btn-danger">
													<i class="fa fa-btn fa-trash"></i>
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
