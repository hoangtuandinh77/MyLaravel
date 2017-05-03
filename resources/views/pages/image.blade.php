@extends('layouts.app')

<link rel="stylesheet" type="text/css" href="/css/viewimage.css">
<link rel="stylesheet" type="text/css" href="/css/bootstrap-tagsinput.css">
<script src="/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/js/bootstrap-tagsinput.min.js"></script>
<script type="text/javascript" src="/js/image.js"></script>

@section('content')

<div class="main col-md-10 col-md-offset-2">
	<div id="thumbnail" class="thumbnail col-md-10">
		<img src="{{$image->url}}" class="img-reponsive">
	</div>

	<div class="image-detail col-md-10">
    <div class="image-description col-md-6">
        <h3>
          {{$image->name}}
          @if(!is_null(Auth::user()) && Auth::user()->id == $image->user_id)
          <button type="button" class="btn btn-default" data-toggle="modal" data-target="#myModal">
            <span class="glyphicon glyphicon-pencil"></span>
          </button>

          <div id="myModal" class="modal fade" role="dialog">
            <div class="modal-dialog">

              <form class="form-horizontal" role="form" method="POST" action="/imgupdate">
              <!-- Modal content-->
                <div class="modal-content">
                  <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    {{ csrf_field() }}
                    <input id="image_id" type="hidden" name="image_id" value="{{$image->id}}">
                    <!--h4 class="modal-title"></h4-->
                  </div>
                  <div class="modal-body">
                      <table style="width: 80%" align="center">
                        <tr>
                          <td style="width: 20%;"><label for="name" class="control-label">Name</label></td>
                          <td><input type="text" class="form-control" name="name" value="{{ $image->name }}"></td>
                          <td><label for="isPrivate" class="control-label">&nbsp;Private</label></td>
                          <td><input type="checkbox" name="isPrivate" style="width: 30px; height: 30px;" {{$image->isPrivate?"checked":""}} ></td>
                        </tr>
                        <tr><td colspan="4">&nbsp;</td></tr>
                        <tr>
                          <td><label for="description" class="control-label">Desc</label></td>
                          <td colspan="3"><textarea type="text" class="form-control" name="description">{{ $image->description }}</textarea></td>
                        </tr>
                        <tr><td colspan="4">&nbsp;</td></tr>
                        <tr>
                          <td><label for="tags" class="control-label">Tags</label></td>
                          <td colspan="3">
                            <input type="text" value="<?php 
                              $tagsVal = ""; 
                              if(count($tags) > 0){
                                foreach ($tags as $tag) {
                                  $tagsVal .= $tag->name.",";
                                }
                              }  
                              echo $tagsVal; 
                            ?>" class="form-control bootstrap-tagsinput" data-role="tagsinput" name="tags">  
                          </td>
                        </tr>
                      </table>

                  </div>
                  <div class="modal-footer">
                    <div style="width: 90%;">
                      <input type="submit" class="btn btn-default" value="Save" />
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
          @endif
        </h3>
        <p>{{$image->description}}</p>
        @if(count($tags)>0)
          <p>
            @foreach($tags as $tag)
              <span class="tag">{{'#'.$tag->name}}</span>
            @endforeach
          </p>
        @endif
        <p>Uploaded on <?php $d = new DateTime($image->created_at); print $d->format('d-m-Y');?> by <a href="/{{$username}}">{{$username}}</a></p>
    </div>
		<div class="view-state col-md-4 pull-right" align="right">
      <span id="heart-icon" class="glyphicon glyphicon-heart font-md" onclick="like();">{{$like}}</span>&nbsp;
      <a id="comment-box-link" href="#comment-box"><span id="comment-icon" class="glyphicon glyphicon-comment font-md">{{count($comments)}}</span>&nbsp;</a>
      <span id="view-icon" class="glyphicon glyphicon-eye-open font-md">{{$image->viewcount}}</span><br>
       @if(!is_null(Auth::user()))
          <button id="btn-select-gallery" type="button" class="btn btn-default" onclick="get_galleries();">
            <span class="glyphicon glyphicon-tag"></span>&nbsp;Add to gallery
          </button>
          <div id="form-select-gallery" style="display: none;">
            <select id="gallery-list" class="form-control">
              
            </select>
            <button type="button" class="btn btn-default" onclick="select_gallery();" style="float: left; margin-top: 2px;">
              <span class="glyphicon glyphicon-plus"></span>&nbsp;Add to gallery
            </button>
            <button type="button" class="btn btn-default" onclick="cancel_select_gallery();" style="margin-top: 2px;">
              <span class="glyphicon glyphicon-remove"></span>&nbsp;Cancel
            </button>
          </div>
        @endif
    </div>
	</div>

	<div id="comments-list" class="comments-list col-md-10">
    <h1>Comments</h1>
 		@forelse($comments as $comment)
		      <div class="media">
     		 <p class="pull-right">
     			<small>
     			<?php
            $created_at = new DateTime($comment->created_at);
            $now = new DateTime(date('Y-m-d H:i'));
            $diff = $created_at->diff($now);
     				if($diff->format('%y') > 0)
     					print $diff->format('%y').' years ago';
     				elseif($diff->format('%m') > 0)
     					print $diff->format('%m').' months ago';
     				elseif($diff->format('%d') > 0)
     					print $diff->format('%d').' days ago';
     				elseif($diff->format('%h') > 0)
     					print $diff->format('%h').' hours ago';
     				elseif($diff->format('%i') > 0)
     					print $diff->format('%i').' minutes ago';
     				else
     					print '1 minute ago';
     			?>
     			</small>
     		</p>
    		<a class="media-left" href="#">
      			<img src="http://i.imgur.com/yuYXx6M.jpg" style="width: 50px; height: 50px;">
    		</a>
      	<div class="media-body">
        		<h4 class="media-heading user_name">{{$comment->name}}</h4>
       		{{$comment->content}}
      	</div>
    	</div>
    	<hr>
 		@empty
 		@endforelse
  </div>

 	@if(!is_null(Auth::user()))
	<div id="comment-box" class="col-md-10">
  	<a class="media-left" href="#">
  			<img src="http://i.imgur.com/yuYXx6M.jpg" style="width: 50px; height: 50px;">
  	</a>
  	<div class="media-body">
  		<form id="comment_form" method="post" action="/comment">
  			<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}">
  			<input type="hidden" id="image_id" name="image_id" value="{{ $image->id }}">
        <input type="hidden" id="user_name" name="user_name" value="{{ Auth::user()->name }}">
   			<textarea id="user_comment" class="user-comment form-control" name="user_comment" placeholder="Write a comment"></textarea>
        <input id="btn-submit" type="submit" name="sumbit" value="Send" class="btn btn-default btn-sm" />
   		</form>
  	</div>
	</div>
	@endif

</div>


@endsection
