<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Gallery;
use App\Image;
use App\User;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
class PublicController extends Controller
{
    //
    function viewImage($url){
        $img = Image::where('url','like',$url."%")->first();

        if(is_null($img))
        {
            return view('pages.notfound');
        }

        $img->update(['viewcount' => $img->viewcount+1]);
        $user = User::find($img->user_id)->first();
        $user_name = $user->username;
        $comments = DB::table('imagecomments')
            ->join('users','imagecomments.user_id','=','users.id')
            ->where('imagecomments.image_id','=',$img->id)
            ->select('users.name','users.username','imagecomments.content','imagecomments.created_at')
            ->orderBy('imagecomments.id')
            ->get();
        $like = DB::table('likes')->where('image_id','=',$img->id)->count();
        $tags = DB::table('imagetags')
            ->join('tags','tags.id','=','imagetags.tag_id')
            ->where('imagetags.image_id','=',$img->id)
            ->select('tags.name')
            ->get();
        return view('pages.image')
            ->with('username',$user_name)
            ->with('image',$img)
            ->with('like',$like)
            ->with('tags',$tags)
            ->with('comments',$comments);
    }

    function galimages($id)
    {
        $gal = Gallery::find($id);
        if(is_null($gal)){
            return view('pages.notfound');
        }
        return DB::table('images')
            ->join('imagegallery','imagegallery.image_id','=','images.id')
            ->join('users','users.id','=','images.user_id')
            ->where('imagegallery.gallery_id','=',$id)
            ->where('images.isPrivate','=',0)
            ->select('images.id', 'images.name','images.url','users.username')
            ->get();
    }

    function search(Request $request)
    {
        $request->flash();

        $popTagIds = ImageTag::select(DB::raw("tag_id,count(image_id) as count"))
            ->groupBy("tag_id")->orderBy("count")
            ->take(20)->pluck("tag_id");

        $popTags = Tag::whereIn('id',$popTagIds)->get();

        if($request->has('topic'))
        {
            if($request->topic == "unclassified")
            {
                return view('search.images')
                    ->with('images',$this->search_unclassified_images($request->orderBy,$request->orderType))->with('tags',$popTags);
            }
            else
            {
                $topic = Topic::where('name','=',$request->topic)->first();
                if(!is_null($topic))
                {
                    return view('search.images')
                        ->with('images',$this->search_image_by_topic($topic->id))->with('tags',$popTags);
                }
                else
                    return view('pages.notfound')->with('tags',$popTags);
            }
        }

        $key = $request->key;
        $search_type = $request->type;

        if(is_null($search_type) || $search_type === 'images')
        {
            if(is_null($key) || trim($key) == "")
            {
                return view('search.images')
                    ->with('images',$this->search_images_all($request->orderBy,$request->orderType))->with('tags',$popTags);
            }
            else
            {
                return view('search.images')
                    ->with('images',$this->search_images_by_tag($key))->with('tags',$popTags);
            }
        }
        //search galleries
        elseif($search_type === 'galleries')
        {

            if(is_null($key) || trim($key) == "")
            {
                return view('search.galleries')
                    ->with('galleries',$this->search_galleries_all())->with('tags',$popTags);
            }
            else
            {
                return view('search.galleries')
                    ->with('galleries',$this->search_galleries_by_name($key))->with('tags',$popTags);
            }
        }
        else
        {
            return view('pages.notfound');
        }
    }

    function search_unclassified_images($orderBy = null, $orderType = null)
    {
        $image_ids = DB::table('images')
            ->leftJoin('imagetags','images.id','=','imagetags.image_id')
            ->groupBy('images.id')
            ->havingRaw("count(imagetags.image_id) = 0")
            ->pluck('images.id');
        $images = null;
        if(is_null($orderBy) || is_null($orderType))
        {
            $images =  DB::table('images')->select('images.*',
                DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','desc')->paginate(20);
        }
        else
        {
            if($orderBy=='new' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','asc')->paginate(20);
            }
            elseif($orderBy=='new' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','desc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.viewcount','asc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.viewcount','desc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('liek','asc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('liek','desc')->paginate(20);
            }
        }

        return $images;
    }

    function search_image_by_topic($topic_id, $orderBy = null, $orderType = null)
    {
        $tag_ids = Tag::where('topic_id','=',$topic_id)->select('id')->get();
        $image_ids = ImageTag::whereIn('tag_id',$tag_ids)->groupBy('image_id')->pluck('image_id');
        $images = null;
        if(is_null($orderBy) || is_null($orderType))
        {
            $images =  DB::table('images')->select('images.*',
                DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','desc')->paginate(20);
        }
        else
        {
            if($orderBy=='new' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','asc')->paginate(20);
            }
            elseif($orderBy=='new' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.id','desc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.viewcount','asc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('images.viewcount','desc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('liek','asc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)->orderBy('liek','desc')->paginate(20);
            }
        }
        return $images;
    }

    function search_images_all($orderBy = null, $orderType = null){
        $images = null;
        if(is_null($orderBy) || is_null($orderType))
        {
            $images =  DB::table('images')->select('images.*',
                DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('images.id','desc')->paginate(20);
        }
        else
        {
            if($orderBy=='new' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('images.id','asc')->paginate(20);
            }
            elseif($orderBy=='new' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('images.id','desc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('images.viewcount','asc')->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('images.viewcount','desc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='asc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('liek','asc')->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='desc')
            {
                $images =  DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek'))->where('images.isPrivate','=',0)->orderBy('liek','desc')->paginate(20);
            }
        }
        return $images;
    }

    function search_images_by_tag($key, $orderBy = null, $orderType = null){
        $tag_ids = Tag::where('name','like',"%".$key."%")->select('id')->get();
        $image_ids = ImageTag::whereIn('tag_id',$tag_ids)
            ->select('image_id')->get();
        $images = null;
        if(is_null($orderBy) || is_null($orderType)){
            $images = DB::table('images')->select('images.*',
                DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
            )
                ->where('images.isPrivate','=',0)->whereIn('images.id',$image_ids)
                ->orWhere('images.name','like','%'.$key.'%')
                ->orderBy('images.id','desc')->paginate(20);
        }
        else{
            if($orderBy=='new' && $orderType=='asc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('images.id','asc')
                    ->paginate(20);
            }
            elseif($orderBy=='new' && $orderType=='desc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('images.id','desc')
                    ->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='asc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('images.viewcount','asc')
                    ->paginate(20);
            }
            elseif($orderBy=='view' && $orderType=='desc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('images.viewcount','desc')
                    ->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='asc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('liek','asc')
                    ->paginate(20);
            }
            elseif($orderBy=='like' && $orderType=='desc')
            {
                $images = DB::table('images')->select('images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                    ->where('images.isPrivate','=',0)
                    ->whereIn('images.id',$image_ids)
                    ->orWhere('images.name','like','%'.$key.'%')
                    ->orderBy('liek','desc')
                    ->paginate(20);
            }
        }
        return $images;
    }

    function search_galleries_all(){
        $galleries = DB::table('galleries')
            ->join('users','users.id','=','galleries.user_id')
            ->where('galleries.isPrivate','==',0)
            ->select('galleries.*', 'users.username')
            ->orderBy('galleries.id','desc')
            ->paginate(20);
        return $galleries;
    }

    function search_galleries_by_name($name){
        $galleries = DB::table('galleries')
            ->join('users','users.id','=','galleries.user_id')
            ->where('galleries.isPrivate','==',0)
            ->where('galleries.name','like','%'.$name.'%')
            ->select('galleries.*', 'users.username')
            ->orderBy('galleries.id','desc')
            ->paginate(20);
        return $galleries;
    }



    function tags(){
        return Tag::pluck('name')->toArray();
        //return Tag::all();
    }
}
