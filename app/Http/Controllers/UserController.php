<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Like;
use App\Models\Post;
use App\Models\ReplieComment;
use App\Models\Role;
use App\Models\Role_user;
use App\Models\User;
use http\Env\Response;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getdata()
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            $user = User::find($userId);
//            #collection #model #object
//
            foreach ($user->roles as $role) {
                echo $role->pivot->role_id;
            }
            #authentication vs #authoriazation
            #collection #all_methods of collection
            #array vs #collection

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function posts(Request $request)
    {
//        $user = User::find($userId)->first();
        $user=auth()->user();
        $userId=$user['id'];
        if (!$userId) {
            return null;
        }
        try {
            $postInsert = Post::create([
                'user_id' => $userId,
                'title' => $request->title
            ]);

            return response()->json(['InsertPostData' => $postInsert]);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function updatePost(Request $request, $postId)
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            if (!$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $userPost = $userId->posts()->where('id', $postId)->first();

            if ($userPost) {
                $userPost->update(['title' => $request->title]);
            }

            return response()->json(['updatePostData' => $userPost]);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function deletePost(Request $request, $postId)
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            if (!$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $userPost = $userId->posts()->where('id', $postId)->first();

            if ($userPost) {
                $userPost->delete();
            }

            return response()->json(['message' => "Post Deleted!"], 200);

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function comments(Request $request, $postId, $userId)
    {
        try {
            $post = Post::find($postId)->first();
            $user=auth()->user();
            $userId=$user['id'];

            if (!$post) {
                return null;
            }

            $commentInsert = Comment::create([
                'user_id' => $userId,
                'post_id' => $post->id,
                'message' => $request->message
            ]);

            return response()->json(['data' => $commentInsert]);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function updateComments(Request $request, $commentId)
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            if (!$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $userComment = $userId->comments()->where('id', $commentId)->first();

            if ($userComment) {
                $userComment->update(['message' => $request->message]);
            }

            return response()->json(['updatecomment' => $userComment], 200);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function deleteComments(Request $request, $commentId)
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            if (!$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $userComment = $userId->comments()->where('id', $commentId)->first();

            if ($userComment) {
                $userComment->delete();
            }

            return response()->json(['message' => 'Record successfully deleted!'], 200);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function replieComments(Request $request, $commentId)
    {
        try {
            $comment = Comment::find($commentId)->first();
            $user=auth()->user();
            $userId=$user['id'];

            if (!$comment && !$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $replieCommentInsert = ReplieComment::create([
                'user_id' => $userId,
                'comment_id' => $comment->id,
                'replie_msg' => $request->replie_msg
            ]);

            return response()->json(['ReplieCommentData' => $replieCommentInsert]);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function postlikes(Request $request, $postId)
    {
        $updateLike = false;
        $isLike = true;

        try {
            $post = Post::find($postId);
            $user=auth()->user();
            $userId=$user['id'];

            if (!$post && !$userId) {
                return response()->json(['Error_message' => 'Not found'], 404);
            }

            $like = $userId->likes()->where('post_id', $post->id)->first();

            if ($like) {
                $alreadyLike = $like->like;
                $updateLike = true;

                if ($alreadyLike == $isLike) {
                    $like->delete();
                    return response()->json(['message' => 'successfully undo like', 'likeDelete_detail' => $like]);
                }
            } else {
                $like = new Like();
            }
            $like->like = $isLike;
            $like->user_id = $userId;
            $like->post_id = $post->id;

            if ($updateLike) {
                $like->update();
                return response()->json(['message' => 'successfully update like', 'likeUpdate_detail' => $like]);
            } else {
                $like->save();
                return response()->json(['message' => 'successfully insert like', 'likeInsert_detail' => $like]);
            }

        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function roles(Request $request)
    {
        try {
            $role = Role::create([
                'role' => $request->role
            ]);

            return response()->json(['role' => $role], 200);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }

    public function roleUsers(Request $request, $roleId)
    {
        try {
            $user=auth()->user();
            $userId=$user['id'];

            $roleUsers = Role_user::create([
                'user_id' => $userId,
                'role_id' => $roleId
            ]);

            return response()->json(['pivotData' => $roleUsers], 200);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }
    }
}
