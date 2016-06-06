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
			  
			  		<a href="/warranty" class="btn btn-primary">New</a>
			
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
					
					<a href="/tags?category_id={{ $category_id }}" class="btn btn-default"><span class="glyphicon glyphicon-cog"></span></a>

				</div>
			</div>
			
			<form action="{{ url('warranties') }}" method="GET" class="form-horizontal" id="form-search">
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

			var tags = new Bloodhound({
			    datumTokenizer: function (d) {
			            return Bloodhound.tokenizers.whitespace(d.isim);
			        },
			    queryTokenizer: Bloodhound.tokenizers.whitespace,
		        remote: {
		            url: '/tags/search/?',
					cache:false,
		            replace: function(url, query) {
		                return url + 'q=' + query + '&category_id={{ $category_id }}';
		            },
		        }
			});
			tags.initialize();
			tags.clearPrefetchCache();

			var elt = $('#search');
			elt.tagsinput({
			  tagClass: 'label label-default',
			  itemValue: 'value',
			  itemText: 'text',
			  typeaheadjs: {
			    name: 'cities',
			    displayKey: 'text',
			    source: tags,
			    limit: 1000,
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
		<div id="unseen-warranties">
		
			<div class="col-sm-2">
				@foreach($tags as $tag)
					
						<a class="tag label label-default" href="/warranties/?search={{ $tag->id }}&btn_search=s">{{ $tag->name }} {{ $tag->getNumberWarranties() }}</a>
				@endforeach

			</div>
			
			<div class="col-sm-10">
		
				<table class="table table-striped warranty-table" id="clickable">
					<thead>
					<tr>
						<th><nobr><a href="{{ createOrderLink('title', $order, $dir, $page) }}">Title</a> <div class="{{ createOrderLinkImage('title', $order, $dir) }}"></div></nobr></th>
						<th><nobr><a href="{{ createOrderLink('date_warranty', $order, $dir, $page) }}">EoW</a> <div class="{{ createOrderLinkImage('date_warranty', $order, $dir) }}"></div></nobr></th>
						<th><nobr><a href="{{ createOrderLink('category_id', $order, $dir, $page) }}">Category</a> <div class="{{ createOrderLinkImage('category_id', $order, $dir) }}"></div></nobr></th>
						<th><nobr>Tags</nobr></th>
						<th>Action</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($warranties as $warranty)
							<tr>
								<!-- td class="table-text"><div>{{ $warranty->id }}</div></td-->
								<td class="table-text">
									<a href="/warranty/{{ $warranty->id }}/update?page={{ $page }}">
										<div>{{ $warranty->title }}</div>
									</a>
								</td>
								<td class="table-text">
								<div class=" {{ getColorDate($warranty->date_warranty) }}">
									@if ($warranty->date_warranty != '0000-00-00')
										{{ date('d.m.Y', strtotime($warranty->date_warranty)) }}
									@endif
								</div>
							</td>
								<td class="table-text"><div class="btn {{ $warranty->css_class }}">{{ $warranty->cname }}</div></td>
								<td class="table-text">
									@foreach(getTags($warranty->tag_ids) as $tag)
										<div style="padding: 3px;" class="tag label label-default">{{ $tag->name }}</div>
									@endforeach
								</td>
															
								<!-- Warranty Action Buttons -->
								<td>
									<nobr>
										<a href="/warranty/{{ $warranty->id }}/delete" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
									</nobr>				
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
				{!! $warranties->appends([])->render() !!}
			</div>
	
	</div>
	
	<script>

		//set cursor to the search field
		$(function () {
				$('#search-text').focus();
		});

		shortcut.add("Ctrl+m",function() { window.location = "/warranty"; });
		shortcut.add("Ctrl+d",function() { window.location = "/warranties/?search=&btn_search=s"; });

	</script>
	
@endsection
