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
				<form action="/person" method="POST" class="form-horizontal">
					{{ csrf_field() }}
					
					<!-- if we are updating a task we need to know the task ID -->
					<input type="hidden" name="person_id" value="{{ $person->id or '' }}" />

					<!-- Person Lastname -->
					<div class="form-group">
						<label for="person-lastname" class="col-sm-2 control-label">Lastname</label>

						<div class="col-sm-10">
							<input type="text" name="lastname" id="person-lastname" class="form-control" value="{{ $person->lastname or old('lastname') }}">
						</div>
					</div>
					
					<!-- Person Firstname -->
					<div class="form-group">
						<label for="person-surname" class="col-sm-2 control-label">Surname</label>

						<div class="col-sm-10">
							<input type="text" name="surname" id="task-surname" class="form-control" value="{{ $person->surname or old('surname') }}">
						</div>
					</div>
					
					<!-- Person Phone -->
					<div class="form-group">
						<label for="person-phone" class="col-sm-2 control-label">Phone</label>

						<div class="col-sm-10">
							<input type="text" name="phone" id="task-phone" class="form-control" value="{{ $person->phone or old('phone') }}">
						</div>
					</div>
					
					<!-- Person Mobile -->
					<div class="form-group">
						<label for="person-mobile" class="col-sm-2 control-label">Mobile</label>

						<div class="col-sm-10">
							<input type="text" name="mobile" id="task-mobile" class="form-control" value="{{ $person->mobile or old('mobile') }}">
						</div>
					</div>
					
					<!-- Person E-Mail -->
					<div class="form-group">
						<label for="person-mail" class="col-sm-2 control-label">E-Mail</label>

						<div class="col-sm-10">
							<input type="text" name="mail" id="task-mail" class="form-control" value="{{ $person->mail or old('mail') }}">
						</div>
					</div>
					
					<!-- Person Brithdate -->
					<div class="form-group">
						<label for="task-name" class="col-sm-2 control-label">Birthdate</label>

						<div class="col-sm-10">
							@if ($person->birthdate > 0)
								<input type="text" name="birthdate" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($person->birthdate)) }}">
							@else
								<input type="text" name="birthdate" id="datepicker" class="form-control" value="{{ old('birthdate') }}">
							@endif
						</div>
					</div>
					
					<!-- Category -->
					<div class="form-group">
						<label for="person-category" class="col-sm-2 control-label">Category</label>
						<div class="col-sm-10">
							<div class="dropdown-category">
							  <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
							     <span class="selection">--not selected--</span>&nbsp;&nbsp;<span class="caret"></span>
							  </button>
							  <ul class="dropdown-menu" id="dropdown-category" aria-labelledby="dropdownMenu1">
							  	@foreach ($categories as $category)
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
							$old_category ="{{ $category_id }}";
								if (!$old_category) {
									$old_category ="{{ $person->category_id }}";
								}
						}
						if ($old_category > 0) {
							$('#category').val($old_category);
							$old_category_name=$("li[js_id='category_"+$old_category+"']").text();
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').text($old_category_name);
							$(".dropdown-category .dropdown-menu li a").parents(".dropdown-category").find('.selection').val($old_category_name);
						};

					</script>
					
					
					<!-- Tags -->
					<div class="form-group">
						<label for="person-mail" class="col-sm-2 control-label">Tags</label>

						<div class="col-sm-10">
							<input type="text" name="tags" id="tags" class="form-control" value="" style="width: 100%;">
						</div>
					</div>
					
					
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
					
					var elt = $('#tags');
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
					  
					</script>
					
					<!-- Action Button -->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary">
								<i class="glyphicon glyphicon-floppy-save"></i> Save Entry
							</button>
							
							<a href="/persons" class="btn btn-warning"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($person->id)
							<nobr>
								<a href="/person/{{ $person->id }}/done" class="btn btn-info"><i class="glyphicon glyphicon-ok"></i> Done</a>
								<a href="/person/{{ $person->id }}/delete" class="delete btn btn-danger"><i class="glyphicon glyphicon-remove"></i> Delete</a>
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
			$('#person-lastname').focus();
		});

	</script>
	
@endsection
