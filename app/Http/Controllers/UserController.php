<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Src\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @param User $userRepository
     */
    public function __construct(User $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function show($id) {
        $authUserID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $user = $this->userRepository->with([
            'medias.user',
            'medias.favorites',
            'medias.comments.user',
            'medias.downloads',
            'followers',
            'followings'
        ])->find($id);

        $user->medias->map(function($media) use ($authUserID) {
            $media->isFavorited = $media->favorites->contains($authUserID);
            $media->isDownloaded = $media->downloads->contains($authUserID);
        });

        $user->followers->map(function($follower) use ($authUserID) {
//            if(!$follower->id == $authUserID) {
                $follower->isFollowing = $follower->followers->contains($authUserID);
                $follower->isFollower = true;
//            }
            unset($follower->followers);
        });

        $user->followings->map(function($following) use ($authUserID) {
//            if(!$following->id == $authUserID)  {
                $following->isFollower = $following->followings->contains($authUserID);
                $following->isFollowing = true;
//            }
            unset($following->followings);
        });

        return response()->json(['data'=>$user]);
    }


    public function getUserMedias(Request $request,$id)
    {
        $authUserID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $user = $this->userRepository->with(['medias'])->find($id);
//        $user->medias->map(function($media) use ($authUserID) {
//            $media->isFavorited = $media->favorites->contains($authUserID);
//            $media->isDownloaded = $media->downloads->contains($authUserID);
//        });
        return response()->json(['data'=>$user]);
    }

    public function getUserFollowers(Request $request,$id)
    {
        $authUserID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $user = $this->userRepository->with(['followers'])->find($id);
        $user->followers->map(function($follower) use ($authUserID) {
            $follower->isFollowing = $follower->followers->contains($authUserID);
            $follower->isFollower = true;
            unset($follower->followers);
        });
        return response()->json(['data'=>$user]);
    }

    public function getUserFollowings(Request $request,$id)
    {
        $authUserID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $user = $this->userRepository->with(['followings'])->find($id);
        $user->followings->map(function($following) use ($authUserID) {
            $following->isFollower = $following->followings->contains($authUserID);
            $following->isFollowing = true;
            unset($following->followings);
        });
        return response()->json(['data'=>$user]);
    }
}
