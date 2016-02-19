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
			  
			  		<a href="/note" class="btn btn-primary">New</a>
			
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
			
				</div>
			</div>
			
			<div class="col-sm-4" style="padding-bottom: 6px;">
				<form action="{{ url('notes') }}" method="GET" class="form-horizontal">
           			 {!! csrf_field() !!}
					<input type="text" name="search" id="search" class="form-control" placeholder="Search" value="{{ $search or '' }}">
					<input type="hidden" name="btn_search" id="search" value="s">
           		</form>
			</div>
			
		</div>
		<div id="unseen">
			<table class="table table-striped note-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('title', $order, $dir, $page) }}">Title</a> <div class="{{ createOrderLinkImage('title', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr>Tags</nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($notes as $note)
						<tr>
							<!-- td class="table-text"><div>{{ $note->id }}</div></td-->
							<td class="table-text">
								<a href="/note/{{ $note->id }}/update">
									<div>{{ $note->title }}</div>
								</a>
							</td>
							<td class="table-text"><div class="btn {{ $note->css_class }}">{{ $note->cname }}</div></td>
							<td class="table-text"><div></div></td>
														
							<!-- Note Action Buttons -->
							<td>
								<nobr>
									<a href="/note/{{ $note->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $notes->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search').focus();
		});

	</script>
	
@endsection
