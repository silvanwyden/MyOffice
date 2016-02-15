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
			<div class="col-sm-6" style="padding-bottom: 6px;">
			  <div class="btn-group" role="group" aria-label="first">
			  
			  		<a href="/person" class="btn btn-primary">New</a>
			
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
					
					@if ($filter_parent == 1)
						<a href="/persons?filter_parent=-1" class="btn btn-default active">Parent</a>
					@else
						<a href="/persons?filter_parent=1" class="btn btn-default">Parent</a>
					@endif
					
					@if ($filter_child == 1)
						<a href="/persons?filter_child=-1" class="btn btn-default active">Child</a>
					@else
						<a href="/persons?filter_child=1" class="btn btn-default">Child</a>
					@endif
									
					<a href="/persons/excel" class="btn btn-default"><span class="glyphicon glyphicon-export"></span></a>	
			
				</div>
			</div>
			
			<form action="{{ url('persons') }}" method="GET" class="form-horizontal" id="form-search">
           			 {!! csrf_field() !!}
				
				<div class="col-sm-3" style="padding-bottom: 6px;">
	           		
	           		@if (count($tags_sel) > 0)
	           			<input type="text" name="search" id="search" class="form-control">
           			@else
           				<input type="text" name="search" id="search" class="form-control" placeholder="Tags">
           			@endif

           		</div>
				
				<div class="col-sm-3" style="padding-bottom: 6px;">
					<input type="text" name="search_text" id="search-text" class="form-control" placeholder="Search" value="{{ $search_text or '' }}">
           		</div>
        		
        		<input type="hidden" name="btn_search" id="search" value="s">
           		
			</form>
			
			<script>
					var cities = new Bloodhound({
					  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('text'),
					  queryTokenizer: Bloodhound.tokenizers.whitespace,
					  local: [ 
							  @foreach ($tags as $tag)
							  	 { "value": {{ $tag->id }} , "text": "{{ $tag->name }}"   , "label": "{{ $tag->css_class }}"    },
							  @endforeach
					         ]
					});
					cities.initialize();
					
					var elt = $('#search');
					elt.tagsinput({
					  tagClass: function(item) {
					    switch (item.label) {
					      case 'label-primary'   : return 'label label-primary';
					      case 'label-danger'  : return 'label label-danger label-important';
					      case 'label-success': return 'label label-success';
					      case 'label-default'   : return 'label label-default';
					      case 'label-warning'     : return 'label label-warning';
					    }
					  },
					  itemValue: 'value',
					  itemText: 'text',
					  typeaheadjs: {
					    name: 'cities',
					    displayKey: 'text',
					    source: cities.ttAdapter()
					  }
					});
					
					 @foreach ($tags_sel as $tag)
					  	 elt.tagsinput('add', { "value": {{ $tag->id }} , "text": "{{ $tag->name }}"   , "label": "{{ $tag->css_class }}"    });
					 @endforeach

					 $('#search').change(function() {
				          $('#form-search').submit();
				       });

					 $('#search-text').change(function() {
				          $('#form-search').submit();
				       });
					  
					</script>
			
		</div>
		<div id="unseen-persons">
			<table class="table table-striped task-table" id="clickable">
				<thead>
				<tr>
					<th><nobr><a href="{{ createOrderLink('lastname', $order, $dir, $page) }}">Name</a> <div class="{{ createOrderLinkImage('lastname', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('phone', $order, $dir, $page) }}">Phone</a> <div class="{{ createOrderLinkImage('phone', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('mobile', $order, $dir, $page) }}">Mobile</a> <div class="{{ createOrderLinkImage('mobile', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('birthday', $order, $dir, $page) }}">Birthday</a> <div class="{{ createOrderLinkImage('birthday', $order, $dir) }}"></div></nobr></th>
					<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
					<th>Tags</th>
					<th>Parent</th>
					<th>Action</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($persons as $person)
						<tr>
							<td class="table-text"><a href="/person/{{ $person->id }}/update">{{ $person->searchname }}</a></td>
							<td class="table-text">{{ $person->phone }}</td>
							<td class="table-text">{{ $person->mobile }}</td>
							<td class="table-text">
								@if ($person->birthdate != '0000-00-00')
									<div class=" {{ getColorBirthdate($person->birthday) }}">
										{{ date('d. F', strtotime('2000-' . $person->birthday)) }}
									</div>
								@endif
							</td>
							<td class="table-text"><div class="btn {{ $person->css_class }}">{{ $person->cname }}</div></td>
							<td class="table-text">
								@foreach(getTags($person->tag_ids) as $tag)
									<div style="padding: 3px;" class="tag label {{ $tag->css_class }}">{{ $tag->name }}</div>
									
								@endforeach
							</td>
							<td class="table-text">{{ getParentPerson($person->parent_id) }}</td>
							
							<!-- Task Action Buttons -->
							<td>
								<nobr>
									<a href="/person/{{ $person->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</nobr>				
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
			{!! $persons->appends([])->render() !!}
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search-text').focus();
		});

	</script>
	
@endsection
