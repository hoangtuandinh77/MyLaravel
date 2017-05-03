@extends('layouts.search')
<link rel="stylesheet" type="text/css" href="/css/gallery.css">
<script type="text/javascript" src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript" src="/js/slider.js"></script>
@section('result')
<div id="galleries">
  @forelse($galleries as $gallery)

  	<div id="{{ $gallery->id.'-gallery' }}" class="card pull-left" style="width: 20rem;">
  	  <img class="card-img-top" width="100%" height="180" src="/images/{{$gallery->img_url}}" alt="No Image">
  	  <div class="card-block">
  	    <h4 class="card-title">{{$gallery->name}}</h4>
  	    <p class="card-text">{{$gallery->description}}</p>
  	    <p class="card-text"><a href="/{{$gallery->username}}">{{$gallery->username}}</a></p>
  	  </div>
  	</div>

  @empty
    <p>Result not found</p>
  @endforelse

  <!-- pagination -->

  <div id="paginate" class="col-md-10" align="center">
  {{ $galleries->links() }}
  </div>

  <!-- slide image -->
  <div id="slider" class="modal fade" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
          
        <div id="carousel-generic" class="carousel slide" data-ride="carousel">
          <!-- Wrapper for slides -->
          <div id="carousel-inner" class="carousel-inner">
           
              <!-- Put images here to slide -->

          <!-- Controls -->
          
          </div>
        </div>

      </div>
    </div>
  </div>


</div>
@endsection