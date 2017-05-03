@extends('layouts.app')
<link rel="stylesheet" type="text/css" href="/css/search.css">
<script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/js/search.js"></script>
@section('content')
<div class="col-md-12 col-md-offset-1">
 	<div class="col-md-10">
	 	<div class="panel panel-default col-md-9">
	 		<div class="panel-heading">
              <div class="container-fluid">
                <ul class="nav navbar-nav">
                  <li><a id="nav-images" class="nav-link">Images</a></li>
                  <li><a id="nav-galleries" class="nav-link">Galleries</a></li>
                </ul>
              </div>
	 		</div>
	 		<div class="panel-body">
	 			@yield('result')

	 		</div>
	 	</div>

	 	<div class="col-md-3">
			<div class="panel panel-default">
				<div class="panel-heading">
		 			<h1>Topic</h1>
		 		</div>
		 		<div class="panel-body">
		 			<ul>
		 				<li><a href="/search?topic=animal">Animal</a></li>
		 				<li><a href="/search?topic=architecture">Architecture</a></li>
		 				<li><a href="/search?topic=art">Art</a></li>
		 				<li><a href="/search?topic=city">City</a></li>
		 				<li><a href="/search?topic=country">Country</a></li>
		 				<li><a href="/search?topic=nature">Nature</a></li>
		 				<li><a href="/search?topic=people">People</a></li>
		 				<li><a href="/search?topic=sport">Sport</a></li>
		 				<li><a href="/search?topic=travel">Travel</a></li>
		 				<li><a href="/search?topic=transport">Transport</a></li>
		 				<li><a href="/search?topic=unclassified">Unclassified</a></li>
		 			</ul>
		 		</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
	 				<h1>Popular Tags</h1>
	 			</div>
		 		<div class="panel-body">
		 			
		 			@if(count($tags)>0)
			            @foreach($tags as $tag)
			              <a href="/search?key={{$tag->name}}">{{'#'.$tag->name}}</a>
			            @endforeach
			        @endif

		 		</div>
			</div>
		</div>
	</div>
</div>
@endsection