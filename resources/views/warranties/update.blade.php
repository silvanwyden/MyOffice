@extends('layouts.app')

@section('content')
	<script src="/dropzone.js"></script>
	<link rel="stylesheet" href="/dropzone.css">
	<div class="container">
	
		<div class="flash-message">
		    @foreach (['danger', 'warning', 'success', 'info'] as $msg)
		      @if(Session::has('alert-' . $msg))
		
		      <p class="alert alert-{{ $msg }}">{{ Session::get('alert-' . $msg) }} <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a></p>
		      @endif
		    @endforeach
	  	</div> <!-- end .flash-message -->
	
		<form action="/warranty" method="POST" class="form-horizontal" id="myform">
			{{ csrf_field() }}
	
			<div class="row" style="padding-bottom: 15px;">
				<div class="col-sm-8">
				  <div class="btn-group" role="group" aria-label="first">
				  
				  		<a href="/warranties?page={{ $page }}" class="btn btn-default"><span class="glyphicon glyphicon-th-list"></span></a>
				  		<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-floppy-save"></span> Save</button>
				  		<button type="submit" class="btn btn-info" name="save_edit" value="save_edit" ><span class="glyphicon glyphicon-floppy-saved"></span> Save&Edit</button>
			  			<a href="/tags?category_id={{ $category_id }}" class="btn btn-default"><span class="glyphicon glyphicon-cog"></span></a>

					</div>
				</div>
			</div>
		
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
					
						<div class="col-sm-10">
							Warranty
						</div>
					
						<div class="col-sm-2" style="text-align: right;">
					
							@if ($counter > 0)
								{{ $counter }}/{{ $total }} &nbsp;
						
								@if ($previous_id > 0)
									<a href="/warranty/{{ $previous_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-left"></a>
								@endif
								@if ($next_id > 0)
									<a href="/warranty/{{ $next_id }}/update?page={{ $page }}" class="glyphicon glyphicon-chevron-right"></a>
								@endif
							@endif
							
						</div>
						
					</div>
				</div>
				
				<div class="heading" style="padding:10px;">
					<ul class="nav nav-tabs">
				  		@if ($filetab)
				  			<li>
			  			@else
			  				<li class="active">
			  			@endif
				  			<a data-toggle="tab" href="#general">General</a></li>
				  			
					  	@if ($filetab)
				  			<li class="active">
			  			@else
			  				<li>
			  			@endif
			  				<a data-toggle="tab" href="#files">Files <span class="badge">{{ count($warranty->getFiles()) }}</span></a></li>
					</ul>
				</div>
				
				<div class="tab-content">
					@if ($filetab)
				 		<div id="general" class="tab-pane fade">
			 		@else
			 			<div id="general" class="tab-pane fade in active">
			 		@endif
	
						<div class="panel-body">
							<!-- Display Validation Errors -->
							@include('common.errors')
			
							<!-- if we are updating a task we need to know the task ID -->
							<input type="hidden" name="warranty_id" value="{{ $warranty->id or '' }}" />
			
							<!-- Warranty Name -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Title</label>
			
								<div class="col-sm-10">
									<input type="text" name="title" id="warranty-title" class="form-control" value="{{ $warranty->title or old('title') }}">
								</div>
							</div>
							
							<!-- Category -->
							<div class="form-group">
								<label for="task-category" class="col-sm-2 control-label">Category</label>
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
											$old_category ="{{ $warranty->category_id }}";
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
		
							var tags = new Bloodhound({
							    datumTokenizer: function (d) {
							            return Bloodhound.tokenizers.whitespace(d.isim);
							        },
							    queryTokenizer: Bloodhound.tokenizers.whitespace,
						        remote: {
						            url: '/tags/search/?',
									cache:false,
						            replace: function(url, query) {
						                return url + 'q=' + query + '&category_id=' + $('#category').val();
						            },
						        }
							});
							tags.initialize();
							tags.clearPrefetchCache();
		
							var elt = $('#tags');
							elt.tagsinput({
							  tagClass: 'label label-default',
							  itemValue: 'value',
							  itemText: 'text',
							  typeaheadjs: {
							    name: 'tags',
							    displayKey: 'text',
							    source: tags,
							    limit: 1000,
							  }
							});
							
							@foreach ($tags_sel as $tag)
							  	 elt.tagsinput('add', { "value": {{ $tag->id }} , "text": "{{ $tag->name }}"   , "label": "{{ $tag->css_class }}"    });
							@endforeach
							  
							</script>
										
							<!-- Read/Write -->
							@if ($warranty->id)
							<div class="form-group">
								<label for="task-created" class="col-sm-2 control-label">Read/Write</label>
			
								<div class="col-sm-10" style="padding-top: 7px;">{{ date('d.m.Y G:i:s', strtotime($warranty->created_at)) }} | {{ date('d.m.Y G:i:s', strtotime($warranty->updated_at)) }}</div>
			
							</div>
							@endif
							
							
							<!-- Date Purchase -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Date of Purchase</label>
			
								<div class="col-sm-10">
									@if ($warranty->date_purchase > 0)
										<input type="text" name="date_purchase" id="datepicker" class="form-control" value="{{ date('d.m.Y', strtotime($warranty->date_purchase)) }}">
									@else
										<input type="text" name="date_purchase" id="datepicker" class="form-control" value="{{ date('d.m.Y', time()) }}">
									@endif
								</div>
							</div>
							
							<!-- Warranty in montsh -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Warranty in Months</label>
			
								<div class="col-sm-10">
									<input type="text" name="warranty_months" id="warranty-title" class="form-control" value="{{ $warranty->warranty_months or old('warranty_months') }}">
								</div>
							</div>
							
							<!-- End of Warranty -->
							@if ($warranty->id)
							<div class="form-group">
								<label for="task-created" class="col-sm-2 control-label">End of Warranty</label>
			
								<div class="col-sm-10" style="padding-top: 7px;">{{ date('d.m.Y', strtotime($warranty->date_warranty)) }}</div>
			
							</div>
							@endif
							
							<!-- Location of warranty sheet -->
							<div class="form-group">
								<label for="task-name" class="col-sm-2 control-label">Location of Warrany Sheet</label>
			
								<div class="col-sm-10">
									<input type="text" name="location" id="warranty-title" class="form-control" value="{{ $warranty->location or old('location') }}">
								</div>
							</div>

							<!-- Action Button -->
							<div class="form-group button-group">
								<div class="col-sm-offset-2 col-sm-9">
									<button type="submit" class="btn btn-primary" style="margin-bottom: 5px;">
										<i class="glyphicon glyphicon-floppy-save"></i> Save&nbsp;
									</button>
									
									<input type="hidden" name="save_edit_hidden" id="save-edit-hidden" value=""/>
									
									<button type="submit" name="save_edit" class="btn btn-info" value="save_edit" style="margin-bottom: 5px;">
										<i class="glyphicon glyphicon-floppy-saved"></i> Save&Edit&nbsp;
									</button>
									
									<a href="/warranties?page={{ $page }}" class="btn btn-warning" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-minus"></i> Cancel</a>
									
									@if ($warranty->id)
									<nobr>
										<a href="/warranty/{{ $warranty->id }}/delete" class="delete btn btn-danger" style="margin-bottom: 5px;"><i class="glyphicon glyphicon-remove"></i> Delete</a>
									</nobr>		
									@endif
									
								</div>
							</div>
							
						</div>
					</div>
					</form>
					
					@if ($filetab)
				 		<div id="files" class="tab-pane fade in active">
			 		@else
			 			<div id="files" class="tab-pane fade ">
			 		@endif
					    
					    <!--  show uploaded files -->
						<ul class="list-group" style="padding: 10px";>
							@foreach ($warranty->getFiles() as $file)
								<li class="list-group-item">
									<a  href="/fileentry/open/{{ $file->id }}" target="_blank">{{ $file->original_filename}}</a>&nbsp;
									<a href="/fileentry/get/{{ $file->id }}" class="btn btn-info glyphicon glyphicon-download"></a>
									<a href="/fileentry/delete/{{ $file->id }}?page={{ $page }}" class="delete btn btn-danger glyphicon glyphicon-trash"></a>
								</li>
							@endforeach
						</ul>
					    
					    <!-- File upload -->
					    @if ($warranty->id)
							<div id="dropzone" style="padding: 10px">
							  	<form action="/warranty/{{ $warranty->id }}/upload" class="dropzone needsclick" id="demo-upload">
							  		{{ csrf_field() }}
					      			<div class="dz-message needsclick">Drop files here or click to upload.</div>
							    </form>
							</div>
						@endif
									    
				  	</div>
				
			</div>
	</div>
	
	<script>
	
		//set cursor to the task name field
		$(function () {
			$('#warranty-title').focus();
		});

		shortcut.add("Ctrl+s",function() { $( "#myform" ).submit(); });
		shortcut.add("Ctrl+e",function() { $( "#save-edit-hidden" ).val('save_edit'); $( "#myform" ).submit(); });

	</script>
	
@endsection
