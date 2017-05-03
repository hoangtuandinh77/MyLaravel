@extends('layouts.search')
<link rel="stylesheet" type="text/css" href="/css/image.css">
<script type="text/javascript" src="/js/jquery-3.1.1.min.js"></script>
<script type="text/javascript" src="/js/order.js"></script>

@section('result')
<div id="search-option-box" style="margin-bottom: 2%;" class="col-md-12">
    <div class="col-md-3 pull-right">
      <select id="orderType-box" name="orderType" class="form-control">
        <option value="desc" <?php if(old('orderType')=='desc') echo 'selected'; ?> >Descending</option>
        <option value="asc" <?php if(old('orderType')=='asc') echo 'selected'; ?> >Ascending</option>
      </select>
    </div>
    <div class="col-md-3 pull-right">
      <select id="orderBy-box" name="orderBy" class="form-control">
        <option value="new" <?php if(old('orderBy')=='new') echo 'selected'; ?> >Newest</option>
        <option value="view" <?php if(old('orderBy')=='view') echo 'selected'; ?> >Most viewed</option>
        <option value="like" <?php if(old('orderBy')=='like') echo 'selected'; ?> >Most liked</option>
      </select>
    </div>
</div>
<div id="image-gallery">

    @forelse($images as $image)

    	<div id="{{$image->id}}-responsive" class="responsive">
        <div class="gallery">
          <a href="/images/{{explode('.', $image->url)[0]}}">
            <img name="{{$image->name}}" class=".img-responsive" src="/images/{{$image->url}}" alt="{{$image->name}}" style="width: 100%; height: 150px;">
          </a>
          <div class="desc">
          	<p style="height: 20px; overflow: hidden;">{{$image->name}}</p>
          	<p><span class="glyphicon glyphicon-heart span-likes"></span>{{$image->liek}}
            		<span class="glyphicon glyphicon-comment span-comments"></span>{{$image->commentcount}}
            	</p>
            	<a href="/images/{{$image->url}}" download class="img-link">download</a>
          </div>
        </div>
      </div>



    @empty
    	<p>Result not found</p>
    @endforelse

  <div id="paginate" align="center">
    {{ $images->links() }}
  </div>

</div>
@endsection