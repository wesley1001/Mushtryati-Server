<?php

namespace App\Http\Controllers;

use App\Src\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    private $userRepository;

    /**
     * UserController constructor.
     * @param User $userRepository
     */
    public function __construct(User $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getFavorites(Request $request)
    {
        $user = Auth::guard('api')->user();
        if($user) {
            $user->load('favorites');
            $medias = $user->favorites;
            $medias->map(function($media) use ($user) {
                $media->isFavorited = true;
            });
            $user->paginate(100);
            return response()->json(['data' => $user,'success'=>true]);
        }
        return response()->json(['success' => false,'message'=>'invalid user']);
    }

    public function getDownloads(Request $request)
    {
        $user = Auth::guard('api')->user();
        if($user) {
            $user->load('downloads');
            $medias = $user->downloads;
            $medias->map(function($media) use ($user) {
                $media->isDownloads = true;
            });
            $user->paginate(100);
            return response()->json(['data' => $user,'success'=>true]);
        }
        return response()->json(['success' => false,'message'=>'invalid user']);
    }

    public function commentMedia(Request $request)
    {
        $user =  Auth::guard('api')->user();
        if($user) {
            $mediaID = $request->json('media');
            $comment = $request->json('comment');
            $user->comments()->create([
                'media_id' => $mediaID,
                'comment' => $comment
            ]);
            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function favoriteMedia(Request $request)
    {
        $user =  Auth::guard('api')->user();
        $mediaID = $request->json('media');
        if ($user->favorites->contains('id', $mediaID)) {
            $user->favorites()->detach($mediaID);
            return response()->json(['success' => true, 'isFavorited' => false]);
        } else {
            $user->favorites()->attach($mediaID);
            return response()->json(['success' => true, 'isFavorited' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function downloadMedia(Request $request)
    {
        $user =  Auth::guard('api')->user();
        $mediaID = $request->json('media');
        if ($user->downloads->contains('id', $mediaID)) {
            $user->downloads()->detach($mediaID);
            return response()->json(['success' => true, 'isDownloaded' => false]);
        } else {
            $user->downloads()->attach($mediaID);
            return response()->json(['success' => true, 'isDownloaded' => true]);
        }
        return response()->json(['success' => false]);
    }

    public function followUser(Request $request)
    {
        $user =  Auth::guard('api')->user();
        $follower = $request->json('follower');
        if ($user->followings->contains('id', $follower)) {
            $user->followings()->detach($follower);
            return response()->json(['success' => true, 'followed' => false]);
        } else {
            $user->followings()->sync([$follower]);
            return response()->json(['success' => true, 'followed' => true]);
        }
        return response()->json(['success' => false]);
    }

}
