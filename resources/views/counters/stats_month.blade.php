@extends('layouts.app')

@section('content')

<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

	<div class="container">
	
		<div class="row" style="padding-bottom: 15px;">
			<div class="col-sm-8">
			  <div class="btn-group" role="group" aria-label="first">
			  
			  		
			  		
			  		<a href="/counter" class="btn btn-primary">New</a>
			  		<a href="/counters" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
					<a href="/counter/stats" class="btn btn-default"><span class="glyphicon glyphicon-stats"></span></a>
			  		
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
					
					<a href="/countercategories" class="btn btn-default"><span class="glyphicon glyphicon-cog"></span></a>
			  		
				</div>
			</div>
		</div>
	
		<div class="panel panel-default">
			<div class="panel-heading">
				Counter Statistics grouped by Month
			</div>
			
			<div id="counter-chart" style="height: 250px;"></div>
			
			<script>

			new Morris.Bar({
				  // ID of the element in which to draw the chart.
				  element: 'counter-chart',
				  // Chart data records -- each entry in this array corresponds to a point on
				  // the chart.
				  data: [
					@foreach($cats as $cat)
 						   { cat: '{{ $cat->month }}/{{ $cat->year }} ({{ $cat->items }})', value: {{ $cat->items }} },		
 						   //{ y: '2006', a: 100, b: 90 },	    
 				    @endforeach


//month: 2016-01, cat1: 10, cat2: 20
				    
				  ],
				  // The name of the data record attribute that contains x-values.
				  xkey: 'cat',
				  // A list of names of data record attributes that contain y-values.
				  ykeys: ['value'],
				  // Labels for the ykeys -- will be displayed when you hover over the
				  // chart.
				  labels: ['Items']
			  
				});

			</script>

		</div>
	</div>
	

	
@endsection
