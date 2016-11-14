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
			<div class="col-sm-8" style="padding-bottom: 6px;">
			  <div class="btn-group" role="group" aria-label="first">
			  
			  		<a href="/task" class="btn btn-primary">New</a>
			
					<div class="btn-group" role="group">
						  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						     <span class="selection">{{ $category or "All Categories" }}</span>&nbsp;&nbsp;<span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						  	<li><a href="?category_id=-1"><b>All Categories</b></a></li>
						  	<li role="separator" class="divider"></li>
						  	@foreach ($categories as $category)
							    <li><a href="?category_id={{ $category->id }}">{{ $category->name }}</a></li>
						    @endforeach
						  </ul>
					</div>
		
					<div class="btn-group" role="group">
						  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						     <span class="selection">{{ $stage or "All Stages" }}</span>&nbsp;&nbsp;<span class="caret"></span>
						  </button>
						  <ul class="dropdown-menu" aria-labelledby="dropdownMenu2">
						  	<li><a href="?stage_id=-1"><b>All Stages</b></a></li>
						  	<li role="separator" class="divider"></li>
						  	@foreach ($stages as $stage)
							    <li><a href="?stage_id={{ $stage->id }}">{{ $stage->name }}</a></li>
						    @endforeach
						  </ul>
					</div>
					
					@if ($filter_deadline == 1)
						<a href="/tasks?filter_deadline=-1" class="btn btn-default active" style="height: 34px;"><span class="glyphicon glyphicon-eye-open"></span></a>	
					@else
						<a href="/tasks?filter_deadline=1" class="btn btn-default"><span class="glyphicon glyphicon-eye-open"></span></a>	
					@endif
			
				</div>
			</div>
			
			<div class="col-sm-4" style="padding-bottom: 6px;">
				<form action="{{ url('tasks') }}" method="GET" class="form-horizontal">
           			 {!! csrf_field() !!}
					<input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ $search or '' }}">
					<input type="hidden" name="btn_search" id="search" value="s">
           		</form>
			</div>
			
		</div>
		<div id="unseen">
			<table class="table table-striped task-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('name', $order, $dir, $page) }}">Task</a> <div class="{{ createOrderLinkImage('name', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('priority_id', $order, $dir, $page) }}">Priority</a> <div class="{{ createOrderLinkImage('priority_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('deadline', $order, $dir, $page) }}">Deadline</a> <div class="{{ createOrderLinkImage('deadline', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('stage_id', $order, $dir, $page) }}">State</a> <div class="{{ createOrderLinkImage('stage_id', $order, $dir) }}"></div></nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tasks as $task)
						<tr id="task_{{ $task->id }}">
							<!-- td class="table-text"><div>{{ $task->id }}</div></td-->
							<td class="table-text  {{ isHighestOutdated($task->pname, $task->deadline) }}">
								<a href="/task/{{ $task->id }}/update?page={{ $page }}">
									<div>{{ $task->name }}</div>
								</a>
							</td>
							<td class="table-text"><div class="btn {{ $task->css_class }}">{{ $task->cname }}</div></td>
							<td class="table-text"><div>{{ $task->pname }}</div></td>
							<td class="table-text">
								<div class=" {{ getColorDate($task->deadline) }}">
									@if ($task->deadline != '0000-00-00')
										{{ date('d.m.Y', strtotime($task->deadline)) }}
									@endif
								</div>
							</td>
							<td class="table-text"><div>{{ $task->sname }}</div></td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
									<a href="/task/{{ $task->id }}/plus_week" class="btn btn-info glyphicon">W</a>
									<a href="/task/{{ $task->id }}/plus_month" class="btn btn-info glyphicon">M</a>
									<a href="/task/{{ $task->id }}/done" class="btn btn-primary glyphicon glyphicon-ok"></a>
									<a href="/task/{{ $task->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $tasks->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search').focus();
		});

		shortcut.add("Ctrl+m",function() { window.location = "/task"; });
		shortcut.add("Ctrl+d",function() { window.location = "/tasks/?search=&btn_search=s"; });
		@if ($filter_deadline == 1)
			shortcut.add("Ctrl+i",function() { window.location = "/tasks?filter_deadline=-1"; });
		@else
			shortcut.add("Ctrl+i",function() { window.location = "/tasks?filter_deadline=1"; });
		@endif	

	</script>
	
@endsection
