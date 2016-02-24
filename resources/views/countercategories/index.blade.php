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
			  
			  		<a href="/countercategory" class="btn btn-primary">New</a>
						
				</div>
			</div>
			
			
		</div>
		<div id="unseen-tags">
			<table class="table table-striped task-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('name', $order, $dir, $page) }}">Name</a> <div class="{{ createOrderLinkImage('name', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('inactive', $order, $dir, $page) }}">Inactive</a> <div class="{{ createOrderLinkImage('inactive', $order, $dir) }}"></div></nobr></th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($countercategories as $countercategory)
						<tr>
							<td class="table-text">
								<a href="/countercategory/{{ $countercategory->id }}/update?page={{ $page }}">
									<div>{{ $countercategory->name }}</div>
								</a>
							</td>
							<td class="table-text">{{ $countercategory->inactive }}</td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
									<a href="/countercategory/{{ $countercategory->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $countercategories->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search').focus();
		});

	</script>
	
@endsection
