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
			  
			  		<a href="/counter" class="btn btn-primary">New</a>
			
					
			
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
					<th><nobr><a href="{{ createOrderLink('date', $order, $dir, $page) }}">Date</a> <div class="{{ createOrderLinkImage('name', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('priority_id', $order, $dir, $page) }}">Calories</a> <div class="{{ createOrderLinkImage('priority_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('deadline', $order, $dir, $page) }}">Distance</a> <div class="{{ createOrderLinkImage('deadline', $order, $dir) }}"></div></nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($counters as $counter)
						<tr>
							<td class="table-text">
								<a href="/counter/{{ $counter->id }}/update"><div>{{ date('d.m.Y', strtotime($counter->date)) }}</a>
							</td>
							<td class="table-text"><div class="btn {{ $counter->css_class }}">{{ $counter->cname }}</div></td>
							<td class="table-text">{{ $counter->calories }}</td>
							<td class="table-text">{{ $counter->distance }}</td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
									<a href="/counter/{{ $counter->id }}/done" class="btn btn-info glyphicon glyphicon-ok"></a>
									<a href="/counter/{{ $counter->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $counters->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search').focus();
		});

	</script>
	
@endsection
