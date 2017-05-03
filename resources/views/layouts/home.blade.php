@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <nav class="navbar navbar-default">
              <div class="container-fluid">
                <div class="navbar-header">
                  <a class="navbar-brand" href="{{URL::to('/')}}">{{ config('app.name', 'Laravel') }}</a>
                </div>
                <ul class="nav navbar-nav">
                  <li class="active"><a href="{{URL::to($user->username.'/images')}}">Images</a></li>
                  <li><a href="{{URL::to($user->username.'/galleries')}}">Galleries</a></li>
                </ul>
              </div>
            </nav>
        </div>
         @yield('main')
    </div>
</div>
@endsection
