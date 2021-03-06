<html>
	<head>
		<link rel="stylesheet" href="/assets/bootstrap.min.css">
		<style>
			.explainer{color:#666; font-size: 80%;}
			.pgp_public,.pgp_message{font-size: 50%;max-width: 340px;}
			.message .panel-body{overflow-x: auto;}
			.message-push-left{margin-right: 40px;}
			.message-push-right{margin-left: 40px;}
			.product-image{width: 100%;padding-bottom: 60%;max-width: 600px;background-size: cover; background-position: center center;}
			.panel-btn{margin-left: 5px;float: right;}
		</style>
	</head>
	<body>
		<header class="navbar navbar-inverse navbar-top" style="border-radius: 0 !important;">
			<div class="container">
		    	<div class="navbar-header">
		    		<a href="/" class="navbar-brand hidden-xs hidden-sm">{{{Settings::get('site_name')}}}</a>
		    	</div>
		    	<nav class="">
		      		<ul class="nav navbar-nav navbar-right">
		        		@if(Auth::check())
		        			<li><a href="/products/create">Add Products</a></li>
		        			<li><a href="/orders">Orders</a></li>
		        			<li><a href="/settings/edit">Settings</a></li>
		        			<li><a href="/logs/errors">Logs</a></li>
		        			<li><a href="/logout">Logout</a></li>
		        		@endif
		        		@if(User::count()>0)
				    		<li><a>1 BTC = {{{get_rates()[Settings::get('currency')]}}} {{{Settings::get('currency')}}}</a></li>
				    	@endif
		      
		      		</ul>
		    	</nav>
			</div>
		</header>
		<div class="container">
			<div class="alert alert-danger" style="display:none;text-align:center;" id="javascriptAlert">
				Javascript is enabled. This may be a security risk.
			</div>
			<script>javascriptAlert.style.display='block'</script>
			@if($errors && count($errors)>0)
				<div class="alert alert-danger">
					<ul>
					@foreach($errors as $error)
						<li>{{{$error}}}</li>
					@endforeach
					</ul>
				</div>
			@endif
			@if(Session::has('successes'))
				<div class="alert alert-success">
					<ul>
					@foreach(Session::get('successes') as $success)
						<li>{{{$success}}}</li>
					@endforeach
					</ul>
				</div>
			@endif
		</div>
		@yield('content')
	</body>
</html>