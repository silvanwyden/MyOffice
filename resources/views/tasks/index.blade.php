@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="col-sm-offset-2 col-sm-8">
		
		
			<div class="flash-message">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
		      @if(Session::has('alert-' . $msg))
		
		      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		      @endif
		    @endforeach
		  </div> <!-- end .flash-message -->
		
			<!-- Current Tasks -->
			
			<a href="/task" class="btn btn-primary">Create New Task</a><br><br>
			
			<!-- Filters -->
			<div class="form-group">
				<div class="col-sm-6">
					<div class="dropdown-stage">
					  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					     <span class="selection">{{ $stage or "--all Stages--" }}</span>&nbsp;&nbsp;<span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					  	<li><a href="?stage_id=-1">--all Stages--</a></li>
					  	@foreach ($stages as $stage)
						    <li><a href="?stage_id={{ $stage->id }}">{{ $stage->name }}</a></li>
					    @endforeach
					  </ul>
					</div>
				</div>


				<div class="col-sm-6">
					<div class="dropdown-category">
					  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
					     <span class="selection">{{ $category or "--all Categories--" }}</span>&nbsp;&nbsp;<span class="caret"></span>
					  </button>
					  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
					  	<li><a href="?category_id=-1">--all Categories--</a></li>
					  	@foreach ($categories as $category)
						    <li><a href="?category_id={{ $category->id }}">{{ $category->name }}</a></li>
					    @endforeach
					  </ul>
					</div>
				</div>
			</div>
			
			
			<br /><br />
			
			
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
											<form class="delete" action="/task/{{ $task->id }}" method="POST">
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
	
		</div>
	</div>
@endsection
