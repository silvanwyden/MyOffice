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
			<div class="col-xs-6 col-sm-4">
				<a href="/task" class="btn btn-primary">Create New Task</a>
			</div>
			
			<div class="col-xs-6 col-sm-4">
				<div style="verticale-align:middle; padding: 6px;">
					#Tasks: {{ $tasks->total() }}
				</div>
			</div>
			
			<br><br>
			
			<div class="col-xs-6 col-sm-4">
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
			
			<div class="col-xs-6 col-sm-4">
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
			
			<div class="col-xs-6 col-sm-4">
				<form action="{{ url('tasks') }}" method="GET" class="form-horizontal">
           			 {!! csrf_field() !!}
           			 
           			<nobr>
					<input type="text" size="2" name="search" id="search" class="form-control" placeholder="Search" value="{{ $search or '' }}">
					
					<button type="submit" name="btn_search" value="s" class="submitbutton" id="submitbutton" style=" background: transparent; border: none !important;font-size:0;">
                    </button>
                    </nobr>
           		</form>
					
			</div>
			

			<br /><br />
			
					<div class="panel-body" id="unseen">
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
									<tr>
										<!-- td class="table-text"><div>{{ $task->id }}</div></td-->
										<td class="table-text">
											<a href="/task/{{ $task->id }}/update">
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
										
										<!-- Task Action Button -->
										<td>
											<nobr>
												<a href="/task/{{ $task->id }}/done" class="btn btn-info glyphicon glyphicon-ok"></a>
												<a href="/task/{{ $task->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-remove"></a>
											</nobr>				
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
						{!! $tasks->appends([])->render() !!}
			
	
		</div>
	</div>
	
	<script>

		  $(function () {
				$('#search').focus();
			});

	</script>
	
@endsection
