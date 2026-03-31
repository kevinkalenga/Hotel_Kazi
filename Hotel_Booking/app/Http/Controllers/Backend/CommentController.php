<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Comment;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Auth;

class CommentController extends Controller
{
     public function StoreComment(Request $request){
        
        Comment::insert([
            'user_id' => $request->user_id,
            'post_id' => $request->post_id,
            'message' => $request->message,
            'created_at' => Carbon::now(),
        ]);

        $notification = array(
            'message' => 'Comment Added Successfully Admin will approved',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }


    public function AllComment(){

        $allcomment = Comment::latest()->get();
        return view('backend.comment.all_comment',compact('allcomment'));

    }
}
