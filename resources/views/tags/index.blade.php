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
			  
			  		<a href="/tag" class="btn btn-primary">New</a>
			
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
			
			
		</div>
		<div id="unseen-tags">
			<table class="table table-striped task-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('name', $order, $dir, $page) }}">Name</a> <div class="{{ createOrderLinkImage('name', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('css_class', $order, $dir, $page) }}">CSS</a> <div class="{{ createOrderLinkImage('css_class', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('seq', $order, $dir, $page) }}">Sequence</a> <div class="{{ createOrderLinkImage('seq', $order, $dir) }}"></div></nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tags as $tag)
						<tr>
							<td class="table-text">
								<a href="/tag/{{ $tag->id }}/update">
									<div>{{ $tag->name }}</div>
								</a>
							</td>
							<td class="table-text"><div class="btn {{ $tag->ccss_class }}">{{ $tag->cname }}</div></td>
							<td class="table-text">{{ $tag->css_class }}</td>
							<td class="table-text">{{ $tag->seq }}</td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
									<a href="/tag/{{ $tag->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $tags->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search').focus();
		});

	</script>
	
@endsection
