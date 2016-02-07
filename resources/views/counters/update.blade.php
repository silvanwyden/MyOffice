@extends('layouts.app')

@section('content')
	<div class="container">
		<div class="panel panel-default">
			<div class="panel-heading">
				New Entry
			</div>

			<div class="panel-body">
				<!-- Display Validation Errors -->
				@include('common.errors')

				<!-- New Task Form -->
				<form action="/counter" method="POST" class="form-horizontal">
					{{ csrf_field() }}
					
					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="counter_id" value="{{ $counter->id or '' }}" />

					<!-- Counter Date -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Date</label>

						<div class="col-sm-10">
							@if ($counter->date > 0)
								<input type="text" name="date" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($counter->date)) }}">
							@else
								<input type="text" name="date" id="datepicker" class="form-control" value="{{ old('date') }}">
							@endif
						</div>
					</div>
					
					<!-- Category -->
					<div class="form-group">
						<label for="counter-category" class="col-sm-2 control-label">Category</label>
						<div class="col-sm-10">
							<div class="dropdown-category">
							  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" id="dropdown-category" aria-labelledby="dropdownMenu1">
							  	@foreach ($countercategories as $category)
								    <li js_id="category_{{ $category->id }}"><a href="#" ref="{{ $category->id }}">{{ $category->name }}</a></li>
							    @endforeach
							  </ul>
							</div>
						</div>
						<input type="hidden" id="category" name="category" value="0">
					</div>
					
					<script>

						//function to show the selected item in the dropdown
						$(".dropdown-category .dropdown-menu li a").click(function(){
							$(this).parents(".dropdown-category").find('.selection').text($(this).text());
							$(this).parents(".dropdown-category").find('.selection').val($(this).text());
							$('#category').val($(this).attr('ref'));
							$('#category_name').val($(this).text());
						});

						//function to load the saved values
						$old_category="{{ old('category') }}";
						if (!$old_category) {
							$old_category ="{{ $counter_category_id }}";
								if (!$old_category) {
									$old_category ="{{ $counter->counter_category_id }}";
								}
						}
						if ($old_category > 0) {
							$('#category').val($old_category);
							$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
						};

					</script>
					
					<!-- Action Button -->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary">
								<i class="glyphicon glyphicon-floppy-save"></i> Save Entry
							</button>
							
							<a href="/counters" class="btn btn-warning"><i class="glyphicon glyphicon-ok"></i> Cancel</a>
							
							@if ($counter->id)
							<nobr>
								<a href="/counter/{{ $counter->id }}/done" class="btn btn-info"><i class="glyphicon glyphicon-ok"></i> Done</a>
								<a href="/counter/{{ $counter->id }}/delete" class="delete btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</a>
							</nobr>		
							@endif
							
						</div>
					</div>
					
				</form>
			</div>
		</div>
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#counter-date').focus();
		});

	</script>
	
@endsection
