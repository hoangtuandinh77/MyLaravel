@extends('layouts.app')

@section('content')
 	<div class="col-md-8 col-md-offset-2">
 	@if (isset($permission))
		<h1>{{$permission}}</h1>
	@else
		<h1>Page not found</h1>
	@endif
	</div>
@endsection