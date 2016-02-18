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
	
		<form action="/person" method="POST" class="form-horizontal">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/persons" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		@if ($person->id)
				  			<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			@endif
			  			
					</div>
				</div>
			</div>
		
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
					
						<div class="col-sm-10">
							Person
						</div>
					
						<div class="col-sm-2" style="text-align: right;">
					
							@if ($counter > 0)
								{{ $counter }}/{{ $total }} &nbsp;
						
								@if ($previous_id > 0)
									<a href="/person/{{ $previous_id }}/update" class="glyphicon glyphicon-chevron-left"></a>
								@endif
								@if ($next_id > 0)
									<a href="/person/{{ $next_id }}/update" class="glyphicon glyphicon-chevron-right"></a>
								@endif
							@endif
							
						</div>
						
					</div>
				</div>
	
				<div class="panel-body">
					<!-- Display Validation Errors -->
					@include('common.errors')
		
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
					
					<!-- Person gender -->
					<div class="form-group">
						<label for="person-gender" class="col-sm-2 control-label">Gender</label>

						<div class="col-sm-10">
							<input type="text" name="gender" id="person-gender" class="form-control" value="{{ $person->gender or old('gender') }}">
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
					
					<!-- Parent ID -->
					<div class="form-group">
						<label for="person-mail" class="col-sm-2 control-label">Parent Person</label>

						<script>
						
						  $(function() {
						  	var availableTags = [
											  	@foreach ($parents as $p)
											  		{key: {{ $p->id }} ,value: '{{ $p->lastname . " " . $p->surname }}'},
											  	@endforeach
							 					];
							
						    $( "#parent_name" ).autocomplete({
						      minLength: 0,
						      source: availableTags,
						      mustMatch: true,
						      focus: function( event, ui ) {
						        $( "#parent_name" ).val( ui.item.value );
						        return false;
						      },
						      select: function( event, ui ) {
						    	  
						        $( "#parent_name" ).val( ui.item.value );
						        $( "#parent_id" ).val( ui.item.key );
						 
						        return false;
						      } 
							  });

						    $("#parent_name").change(function(){
						  		$( "#parent_id" ).val('');
							});
						 
						  });

						</script>
						
						<div class="col-sm-10">
							<input type="hidden" name="parent_id" id="parent_id" class="form-control" value="{{ $person->parent_id }}" style="width: 100%;">
							<input type="text" name="parent_name" id="parent_name" class="form-control" value="{{ getParentPerson($person->parent_id) }}" style="width: 100%;">
						</div>
					</div>

					
					<!-- Person salutation -->
					<div class="form-group">
						<label for="person-salutation" class="col-sm-2 control-label">Salutation</label>

						<div class="col-sm-10">
							<input type="text" name="salutation" id="person-salutation" class="form-control" value="{{ $person->salutation or old('salutation') }}">
						</div>
					</div>

					<!-- Person street -->
					<div class="form-group">
						<label for="person-street" class="col-sm-2 control-label">Street</label>

						<div class="col-sm-10">
							<input type="text" name="street" id="person-street" class="form-control" value="{{ $person->street or old('street') }}">
						</div>
					</div>
					
					<!-- Person plz -->
					<div class="form-group">
						<label for="person-plz" class="col-sm-2 control-label">PLZ</label>

						<div class="col-sm-10">
							<input type="text" name="plz" id="person-plz" class="form-control" value="{{ $person->plz or old('plz') }}">
						</div>
					</div>
					
					<!-- Person city -->
					<div class="form-group">
						<label for="person-city" class="col-sm-2 control-label">City</label>

						<div class="col-sm-10">
							<input type="text" name="city" id="person-city" class="form-control" value="{{ $person->city or old('city') }}">
						</div>
					</div>
					
					<!-- Person country -->
					<div class="form-group">
						<label for="person-country" class="col-sm-2 control-label">Country</label>

						<div class="col-sm-10">
							<input type="text" name="country" id="person-country" class="form-control" value="{{ $person->country or old('country') }}">
						</div>
					</div>
					
					
					<!-- Action Button -->
					<div class="form-group">
						<div class="col-sm-offset-2 col-sm-9">
							<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
							</button>
							
							@if ($person->id)
							<button type="submit" name="save_edit" class="btn btn-info" value="save_edit" style="margin-bottom: 5px;">
								<i class="glyphicon glyphicon-floppy-saved"></i> Save&Edit&nbsp;
							</button>
							@endif
							
							<a href="/persons" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
							
							@if ($person->id)
							<nobr>
								<a href="/person/{{ $person->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
							</nobr>		
							@endif
							
						</div>
					</div>
						
					
				</div>
			</div>
		</form>
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#person-lastname').focus();
		});

	</script>
	
@endsection
