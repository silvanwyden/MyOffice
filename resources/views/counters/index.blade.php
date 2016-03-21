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
					
					<a href="/counter/stats" class="btn btn-default"><span class="glyphicon glyphicon-stats"></span></a>
					<a href="/countercategories" class="btn btn-default"><span class="glyphicon glyphicon-cog"></span></a>
					
				</div>
			</div>
			
			
		</div>
		<div id="unseen-counters">
			<table class="table table-striped task-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('date', $order, $dir, $page) }}">Date</a> <div class="{{ createOrderLinkImage('date', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('counter_category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('counter_category_id', $order, $dir) }}"></div></nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($counters as $counter)
						<tr>
							<td class="table-text">
								<a href="/counter/{{ $counter->id }}/update?page={{ $page }}"><div>{{ date('d.m.Y', strtotime($counter->date)) }}</a>
							</td>
							<td class="table-text"><div class="btn {{ $counter->css_class }}">{{ $counter->cname }}</div></td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
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

		shortcut.add("Ctrl+a",function() { window.location = "/counter"; });

	</script>
	
@endsection
