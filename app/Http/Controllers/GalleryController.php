<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Gallery;
use App\Image;
use Illuminate\Support\Facades\Auth;


use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class GalleryController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }

    function galinfo($id){
        $gal = Gallery::find($id);
        if(!is_null($gal))
        {
            if(Auth::user()->id == $gal->user_id)
            {
                return $gal;
            }
        }
        return NULL;
    }

    function galdelete(Request $request){
        if($request->has('gallery_id'))
        {
            $gal = Gallery::find($request->gallery_id);
            if(!is_null($gal))
            {
                if(Auth::user()->id == $gal->user_id)
                {
                    ImageGallery::where('gallery_id','=',$gal->id)->delete();
                    Gallery::where('id','=',$gal->id)->delete();
                    return 'successed';
                }
            }
        }
        return 'failed';
    }

    function galupdate(Request $request){
        if($request->has('gallery_id') && $request->has('name') && $request->has('isPrivate'))
        {
            $gal = Gallery::find($request->gallery_id);
            if(is_null($gal))
            {
                return 'failed';
            }
            else
            {
                $gal->update(['name'=>$request->name,'isPrivate'=>$request->isPrivate,'description'=>$request->description]);
                return 'successed';
            }
        }
        return 'failed';
    }

    function create(Request $request)
    {
        if(is_null($request->name))
        {
            return Redirect::back()->withErrors('name');
        }

        $gal = new Gallery;
        $gal->name = $request->name;
        $gal->isPrivate = !is_null($request->isPrivate);
        $gal->user_id = Auth::user()->id;
        $gal->description = $request->description;
        $gal->created_at = date('Y-m-d H:i:s');
        $gal->save();

        if ($request->hasFile('images'))
        {
            $files = $request->file('images');
            foreach ($files as $file) {
                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();

                $md5Filename = md5("ximaz".date('Y-m-d H:i:s').Auth::user()->id.$filename);

                $destPath = "../public/images/";

                $file->move($destPath,$md5Filename.".".$extension);

                $img = new Image;
                $img->name = $filename;
                $img->url = $md5Filename.".".$extension;
                if($gal->img_url == "")
                {
                    Gallery::find($gal->id)->update(["img_url"=>$img->url]);
                }
                if($gal->isPrivate == 1)
                {
                    $img->isPrivate = 1;
                }
                $img->user_id = Auth::user()->id;
                $img->created_at = date('Y-m-d H:i:s');
                $img->save();

                $img_gal = new ImageGallery;
                $img_gal->gallery_id = $gal->id;
                $img_gal->image_id = $img->id;

                $img_gal->save();
            }
        }

        return Redirect::back();
    }

    function images($id){
        $gal = Gallery::find($id);
        if(is_null($gal))
        {
            return view('pages.notfound');
        }
        if($gal->user_id == Auth::user()->id)
        {
            return DB::table('images')
                ->join('imagegallery','imagegallery.image_id','=','images.id')
                ->join('users','users.id','=','images.user_id')
                ->where('imagegallery.gallery_id','=',$id)
                ->select('images.id', 'images.name','images.url','users.username')
                ->get();
        }
        else
        {
            if($gal->isPrivate == 1)
                return view('pages.notfound')->with('permission', 'You are not allowed to enter. This is a private page.');
            else
            {
                return DB::table('images')
                    ->join('imagegallery','imagegallery.image_id','=','images.id')
                    ->join('users','users.id','=','images.user_id')
                    ->where('imagegallery.gallery_id','=',$id)
                    ->where('images.isPrivate','=',0)
                    ->select('images.id', 'images.name','images.url','users.username')
                    ->get();
            }
        }
    }

    function imgcomment(Request $request){
        if(is_null($request->user_comment))
        {
            return 'failed';
        }
        else
        {
            $comment = new ImageComment;
            $comment->user_id = Auth::user()->id;
            $comment->image_id = $request->image_id;
            $comment->content = $request->user_comment;
            $comment->created_at = date('Y-m-d H:i:s');
            $comment->save();
            return 'successed';
        }
    }

    function imglike(Request $request)
    {
        if($request->has('image_id'))
        {
            $img = Image::find($request->image_id);
            if(!is_null($img))
            {
                $user_id = Auth::user()->id;
                if(Like::where('image_id','=',$img->id)->where('user_id','=',$user_id)->count()===0)
                {
                    $like = new Like;
                    $like->user_id = $user_id;
                    $like->image_id = $img->id;
                    $like->save();
                    return 'successed';
                }
            }
        }
        return 'failed';
    }

    function imgupdate(Request $request){
        if($request->has('image_id') && $request->has('name'))
        {
            $img = Image::find($request->image_id);
            if(is_null($img))
            {
                return view('pages.notfound');
            }
            else
            {
                $img->update(['name'=>$request->name,'isPrivate'=>!is_null($request->isPrivate),'description'=>$request->description]);

                if($request->has('tags'))
                {
                    ImageTag::where('image_id','=',$img->id)->delete();
                    $tags = explode(",", $request->tags);
                    foreach ($tags as $value) {
                        $tag = Tag::where('name','=',trim($value))->first();
                        if(!is_null($tag))
                        {
                            $img_tag = new ImageTag;
                            $img_tag->image_id = $img->id;
                            $img_tag->tag_id = $tag->id;
                            $img_tag->save();
                        }
                    }
                }
                return Redirect::back();
            }
        }
        return view('pages.notfound');
    }

    function imgdelete(Request $request){
        if($request->has('image_id'))
        {
            $img = Image::find($request->image_id);
            if(is_null($img))
            {
                return view('pages.notfound');
            }
            else
            {
                /*$img->update(['name'=>$request->name,'isPrivate'=>!is_null($request->isPrivate),'description'=>$request->description]);
                return Redirect::back();  */
                $filepath = public_path().'/images/'.$img->url;
                ImageComment::where('image_id','=',$img->id)->delete();
                Like::where('image_id','=',$img->id)->delete();
                ImageGallery::where('image_id','=',$img->id)->delete();
                Image::where('id','=',$img->id)->delete();
                if(File::exists($filepath))
                    File::delete($filepath);
                return 'successed';
            }
        }
        return view('pages.notfound');
    }
    function showUpLoadForm(){
        return "â";
    }
    function upload(Request $request){
        if ($request->hasFile('images'))
        {
            $files = $request->file('images');

            foreach ($files as $file) {

                $filename = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                if($extension === "jpg" || $extension === "png" || $extension === "bmp" || $extension === "jpeg"){
                    $md5Filename = md5("ximaz".date('Y-m-d H:i:s').Auth::user()->id.$filename);

                    $destPath = "../public/images/";

                    $file->move($destPath,$md5Filename.".".$extension);

                    $img = new Image;
                    $img->name = $filename;
                    $img->url = $md5Filename.".".$extension;
                    $img->user_id = Auth::user()->id;
                    $img->created_at = date('Y-m-d H:i:s');
                    $img->save();
                }
            }
        }
        return Redirect::back();
    }

    function mygalleries(){
        if(Auth::check())
        {
            return Gallery::where('user_id','=',Auth::user()->id)->get();
        }
        return NULL;
    }

    function addImg2Gal(Request $request){
        if($request->has('gallery_id') && $request->has('image_id'))
        {
            if(!is_null($request->gallery_id) && !is_null($request->image_id))
            {
                $gal = Gallery::find($request->gallery_id);
                $img = Image::find($request->image_id);
                if(!is_null($gal) && !is_null($img))
                {
                    if(ImageGallery::where('gallery_id','=',$gal->id)->where('image_id','=',$img->id)->count()===0)
                    {
                        $img_gal = new ImageGallery;
                        $img_gal->gallery_id = $gal->id;
                        $img_gal->image_id = $img->id;
                        $img_gal->save();

                        $gal->update(["img_url"=>$img->url]);
                        return 'successed';
                    }
                }
            }
        }
        return 'failed';
    }
}
