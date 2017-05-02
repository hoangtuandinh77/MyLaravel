<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image;
use App\User;
use App\Gallery;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /*
    //
    @return void
    */
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $images = $user->images()
            ->orderBy('images.id','desc')
            ->paginate(20);

        // $images = DB::table('images')
        //             ->where('images.user_id','=',$user->id)
        //             ->select(
        //                 'images.*',
        //                 DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
        //                 DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
        //                 )
        //             ->orderBy('images.id','desc')
        //             ->paginate(20);
        return view('pages.images')
            ->with('user',$user)
            ->with('images',$images);
    }
    public function images($username)
    {
        $user = User::where('username','=',$username)->first();
        if(is_null($user))
            return view('pages.notfound');
        elseif($user->id == Auth::user()->id)
        {
            $images =  DB::table('images')
                ->where('images.user_id','=',$user->id)
                ->select(
                    'images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                ->orderBy('images.id','desc')
                ->paginate(20);
            return view('pages.images')
                ->with('user',$user)
                ->with('images',$images);
        }
        else
        {
            $images = DB::table('images')
                ->where('images.user_id','=',$user->id)
                ->where('images.isPrivate','=',0)
                ->select(
                    'images.*',
                    DB::raw('(select count(*) from imagecomments where imagecomments.image_id = images.id)  as commentcount'),
                    DB::raw('(select count(*) from likes where likes.image_id = images.id)  as liek')
                )
                ->orderBy('images.id','desc')
                ->paginate(20);
            return view('pages.images')
                ->with('user',$user)
                ->with('images',$images);
        }
    }

    public function galleries($username)
    {
        $user = User::where('username','=',$username)->first();
        if(is_null($user))
            return view('pages.notfound');
        elseif($user->id == Auth::user()->id)
            return view('pages.galleries')
                ->with('user',$user)
                ->with('galleries',Gallery::where('user_id','=',$user->id)->orderBy('id','desc')->paginate(20));
        else
            return view('pages.galleries')
                ->with('user',$user)
                ->with('galleries',Gallery::where('user_id','=',$user->id)->where('isPrivate','==',0)->orderBy('id','desc')->paginate(20));
    }
}
