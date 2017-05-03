@extends('layouts.home')
<link rel="stylesheet" type="text/css" href="/css/gallery.css">
<script type="text/javascript" src="{{ asset('js/jquery-3.1.1.min.js') }}"></script>
<script type="text/javascript" src="/js/galleries.js"></script>
@section('main')

<div class="col-md-10 col-md-offset-1">
    <div class="panel panel-default">
      <div class="panel-heading clearfix">
	      <h4 class="panel-title pull-left" style="padding-top: 7.5px;">Galleries</h4>
  	     @if($user->id == Auth::user()->id)  
            <input id="btn-create" type="button" name="Create" class="btn btn-default btn-sm pull-right" data-toggle="modal" data-target="#new-gallery" value="New">
          @endif
	     </div>
        <div class="panel-body">
           <div id="galleries">

              @if($user->id == Auth::user()->id)
              <!--Modal form -->
              <div id="new-gallery" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content -->
                   <form class="form-horizontal" role="form" method="POST" action="/galcreate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="modal-content col-md-10 col-md-offset-2">
                      <div class="modal-header col-md-10 col-md-offset-2">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">New Gallery</h4>
                      </div>
                      <div class="modal-body col-md-10 col-md-offset-2">
                        
                          <label for="name" class="control-label col-md-2">Name</label>

                          <div class="col-md-6">
                            <input type="text" name="name" class="form-control" autocomplete="off" />
                          </div>

                          <label for="isPrivate" class="control-label col-md-2">Private</label>

                          <div class="col-md-2">
                            <input type="checkbox" name="isPrivate" class="form-control" />
                          </div>

                          <label class="control-label col-md-2">Images</label>

                          <div class="col-md-10">
                            <input type="file" accept="image/*" runat="server" name="images[]" multiple class="form-control">
                          </div>

                          <label class="control-label col-md-2">Desc</label>

                          <div class="col-md-10" style="margin-top: 10px;">
                            <textarea type="text" class="form-control" name="description"></textarea>
                          </div>

                        </div>

                      <div class="modal-footer col-md-10 col-md-offset-2">
                        <input type="submit" class="btn btn-default" value="Create" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </form>

                </div>
              </div>


              <div id="update-gallery" class="modal fade" role="dialog">
                <div class="modal-dialog">

                  <!-- Modal content -->
                   <form class="form-horizontal" role="form" method="POST" action="/galupdate" enctype="multipart/form-data">
                        {{ csrf_field() }}
                    <div class="modal-content col-md-10 col-md-offset-2">
                      <div class="modal-header col-md-10 col-md-offset-2">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Update Gallery</h4>
                      </div>
                      <div class="modal-body col-md-10 col-md-offset-2">
                        
                          <input id="update-id" type="hidden" name="gallery_id">
                          <label for="name" class="control-label col-md-2">Name</label>

                          <div class="col-md-6">
                            <input id="update-name" type="text" name="name" class="form-control" autocomplete="off" />
                          </div>

                          <label for="isPrivate" class="control-label col-md-2">Private</label>

                          <div class="col-md-2">
                            <input id="update-isPrivate" type="checkbox" name="isPrivate" class="form-control" />
                          </div>

                          <label class="control-label col-md-2">Desc</label>

                          <div class="col-md-10" style="margin-top: 10px;">
                            <textarea id="update-desc" type="text" class="form-control" name="description"></textarea>
                          </div>

                        </div>

                      <div class="modal-footer col-md-10 col-md-offset-2">
                        <input type="submit" class="btn btn-default" value="Update" />
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      </div>
                    </div>
                  </form>

                </div>
              </div>


              @endif
              <!-- display galleries -->
              @forelse($galleries as $gallery)

                <div id="{{ $gallery->id.'-gallery' }}" class="card pull-left" style="width: 20rem;">
                  <img class="card-img-top" width="100%" height="180" src="/images/{{$gallery->img_url}}" alt="No Image">
                  <div class="card-block">
                    <h4 class="card-title">{{$gallery->name}}</h4>
                    <p class="card-text">{{$gallery->description}}</p>
                    @if($user->id == Auth::user()->id)  
                      <p><a id="{{ $gallery->id.'-galupdate' }}" class="link-update">update</a>&nbsp;<a id="{{ $gallery->id.'-galdelete' }}" class="link-delete">delete</a></p>
                    @endif
                  </div>
                </div>

              @empty
                  <p>Create your own galleries</p>
              @endforelse
              
           </div>
           
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
</div>

@if($errors->any())
  <script type="text/javascript">
    alert("gallery name can't be empty");
    $("#new-gallery").modal("show");
  </script>
@endif  
   

@endsection     