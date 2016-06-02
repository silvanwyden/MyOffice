<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>MyOffice</title>

	<link href="https://fonts.googleapis.com/css?family=Raleway:300,400,500,700" rel="stylesheet" type="text/css">

	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />

	<!-- include libraries(jQuery, bootstrap, fontawesome) -->
	<link href="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet"> 
	<link href="https://netdna.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.css" rel="stylesheet">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.js"></script> 
	<script src="https://netdna.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.js"></script> 
	
	<link href="/custom.css" rel="stylesheet">
	
	<!-- include summernote css/js-->
	<link href="/summernote.css" rel="stylesheet">
	<script src="/summernote.js"></script>
	
    
	<script src="https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.2.20/angular.min.js"></script>
	<script src="https://twitter.github.io/typeahead.js/releases/latest/typeahead.bundle.js "></script>
	<script src="/bootstrap-tagsinput-angular.js"></script>
	<script src="/bootstrap-tagsinput.min.js"></script>
	<script scr="/assets/app.js"></script>
	<link rel="stylesheet" href="/bootstrap-tagsinput.css">
    <link rel="stylesheet" href="/app.css">
    
    <script type="text/javascript" src="/shortcut.js"></script>
    
    <script>
		
	    shortcut.add("Ctrl+1",function() { window.location = "/tasks"; });
	    shortcut.add("Ctrl+2",function() { window.location = "/notes"; });
	    shortcut.add("Ctrl+3",function() { window.location = "/persons"; });
	    shortcut.add("Ctrl+4",function() { window.location = "/counters"; });
	    shortcut.add("Ctrl+5",function() { window.location = "/passpacks"; });
	
    </script>
    
	<style>
		body {
			font-family: 'Raleway';
			margin-top: 25px;
		}

		button .fa {
			margin-right: 6px;
		}

		.table-text div {
			padding-top: 6px;
		}
	</style>

</head>

<body>
	<div class="container" style="padding-top:13px;">
		<nav class="navbar navbar-default">
			<div class="container-fluid">
			
				<div class="navbar-header">
					<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
			
		   	    	<a class="navbar-brand" href="/">
				        <div class="glyphicon glyphicon-cloud"></div>
				    </a>
			
			    </div>
				
				<div id="navbar" class="navbar-collapse collapse">
				    @if (!Auth::guest())
					    <ul class="nav navbar-nav">
					    	<li class="{{ Request::is( 'task*') ? 'active' : '' }}">
					    		<a href="/tasks?page=1">Tasks
									@if(isset($tasks))
										<span class="badge">{{ $tasks->total() }}</span>
									@endif
								</a>
							</li>
							<li class="{{ Request::is( 'note*') ? 'active' : '' }}">
					    		<a href="/notes?page=1">Notes
									@if(isset($notes))
										<span class="badge">{{ $notes->total() }}</span>
									@endif
								</a>
							</li>
				      		<li class="{{ Request::is( 'person*') ? 'active' : '' }}">
				        		<a href="/persons?page=1">Persons
				        			@if(isset($persons))
										<span class="badge">{{ $persons->total() }}</span>
									@endif
				        		</a>
			        		</li>
				        	<li class="{{ Request::is( 'counter*') ? 'active' : '' }}">
				        		<a href="/counters?page=1">Counters
				        			@if(isset($counters))
										<span class="badge">{{ $counters->total() }}</span>
									@endif
				        		</a>
			        		</li>
			        		<li class="{{ Request::is( 'passpack*') ? 'active' : '' }}">
				        		<a href="/passpacks?page=1">PassPacks
				        			@if(isset($passpacks))
										<span class="badge">{{ $passpacks->total() }}</span>
									@endif
				        		</a>
			        		</li>
			        		<li class="{{ Request::is( 'warranties') ? 'active' : '' }}">
				        		<a href="/warranties?page=1">Warranties
				        			@if(isset($passpacks))
										<span class="badge">{{ $passpacks->total() }}</span>
									@endif
				        		</a>
			        		</li>
			        		<li class="{{ Request::is( 'vouchers') ? 'active' : '' }}">
				        		<a href="/vouchers?page=1">Vouchers
				        			@if(isset($passpacks))
										<span class="badge">{{ $passpacks->total() }}</span>
									@endif
				        		</a>
			        		</li>
		        		</ul>
	        		@endif
	        		
	    			<ul class="nav navbar-nav navbar-right">
						@if (Auth::guest())
							<li><a href="/auth/login"><i class="fa fa-btn fa-sign-in"></i>Login</a></li>
						@else
			        		<li class="dropdown">
						    	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog"></span>Settings <span class="caret"></span></a>
					        	<ul class="dropdown-menu">
						            <li><a href="/countercategories">Counter Categories</a></li>
						            <li><a href="/tags">Tags</a></li>
						            <li><a href="#">Stages</a></li>
						            <li><a href="#">Users</a></li>
						        </ul>
					        </li>
					        <li class="dropdown">
						    	<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-user"></span>{{ Auth::user()->name }} <span class="caret"></span></a>
					          	<ul class="dropdown-menu">
						            <li><a href="#">Change Password</a></li>
						            <li><a href="/common/about">About</a></li>
						            <li><a class="glyphicon glyphicon-log-out" href="/auth/logout">Logout</a></li>
					          	</ul>
					        </li>
						@endif
					</ul>
					
				</div>
				
			</div>
		</nav>
	</div>

	@yield('content')
	
  	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
  	<script>

	  	//loadi dateicker for date fields
	  	$(function() {
		  	$( "#datepicker" ).datepicker({ 
			  							dateFormat: 'd.m.yy',
			  							firstDay: 1
			  							 });
	  	});

	  	//security question when deleting an item
	 	$(".delete").click(function(){
	        return confirm("Do you want to delete this item?");
	    });
	  
	  	$(document).ready(function() {

	  		//make table rows clickable
		  	$('#clickable .table-text').click(function() {
			  	window.location.href = $(this).parent().find("a").attr("href");
			});


			//load summernote editor
		  	$('#summernote').summernote({
			  	height: 430,
			  	toolbar: [
					['style', ['bold', 'italic', 'underline', 'strikethrough']],
					['para', ['ul', 'ol',]],
					['misc1', ['link', 'picture', 'table', 'codeview', 'fullscreen']],
							  ],
		  	});

			//remove info boxes after 5 seconds
		  	setTimeout(function() {
		  	  $(".flash-message").slideUp( 300 );
		  	}, 3000);
		  			  	
		  
		});


  	</script>
  	
  	
	

	
</body>
</html>
