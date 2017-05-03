@extends('layouts.home')
<link href="/css/image.css" rel="stylesheet">
<script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
@section('main')
<div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
        <div class="panel-heading clearfix">
	      <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Images</h4>
	      @if($user->id == Auth::user()->id)	
	      	<form id="upload_form" action="/upload" role="form" method="post" enctype="multipart/form-data">
	      	 {{ csrf_field() }}
	      	<label class="btn btn-default btn-file pull-right btn-sm" >Upload<input id="imgInput" type="file" accept="image/*" runat="server" name="images[]" multiple style="display: none;" onchange="$('#upload_form').submit();"></label>
	      	</form>
	      @endif
	    </div>
        <div class="panel-body">

	       <div id="image-gallery">
	       		@forelse($images as $image)

	       			<div id="{{$image->id}}-responsive" class="responsive">
					  <div class="gallery">
					    <a href="/images/{{explode('.', $image->url)[0]}}">
					      <img name="{{$image->name}}" class=".img-responsive" src="/images/{{$image->url}}" alt="{{$image->name}}" style="width: 100%; height: 200px;">
					    </a>
					    <div class="desc">
					    	<p style="height: 20px; overflow: hidden;">{{$image->name}}</p>
					    	<p><span class="glyphicon glyphicon-heart span-likes"></span>{{$image->liek}}
					      		<span class="glyphicon glyphicon-comment span-comments"></span>{{$image->commentcount}}
					      	</p>
					      	<a href="/images/{{$image->url}}" download class="img-link">download</a>
					      	@if($user->id == Auth::user()->id)
					      		&nbsp;
					      		<a id="{{$image->id}}-link-delete" class="img-link link-delete">delete</a>
					      	@endif
					    </div>
					  </div>
					</div>
				@empty
				    <p>Upload your images</p>
				@endforelse
	       </div>
	       <div id="paginate" align="center">
	       		{{ $images->links() }}
	       </div>
	       
	       @if($user->id == Auth::user()->id)
	       <script type="text/javascript">
	       		$(document).ready(function(){
	       			$(".link-delete").click(function(){
		       				result = confirm('Are you really want to delete this image?');
		       				if(result)
		       				{
			       				image_id = parseInt(this.id);
			       				if(image_id != null)
							   	{
							    	var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
							    	$.ajax({
								    type: "post",
								  	data:{'_token':CSRF_TOKEN, 'image_id':image_id},
								    url: '/imgdelete',
								    success: function(msg) {
								    	if(msg=='successed'){
								    		$("#"+image_id+"-responsive").remove();
								    	}
								    }
									});
								}
						}

	       			});
	       		});
	       </script>
	       @endif
@endsection
