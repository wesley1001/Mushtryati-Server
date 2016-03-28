<?php

namespace App\Http\Controllers;

use App\Src\Comment\Comment;
use App\Src\Media\MediaManager;
use App\Src\Media\Media;
use App\Src\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    /**
     * @var Media
     */
    private $mediaRepository;
    /**
     * @var Comment
     */
    private $commentRepository;
    /**
     * @var User
     */
    private $userRepository;
    /**
     * @var ImageManager
     */

    /**
     * MediaController constructor.
     * @param Media $mediaRepository
     * @param Comment $commentRepository
     * @param User $userRepository
     */
    public function __construct(Media $mediaRepository,Comment $commentRepository,User $userRepository)
    {
        $this->mediaRepository = $mediaRepository;
        $this->commentRepository = $commentRepository;
        $this->userRepository = $userRepository;
    }

    // Get Medias
    public function index()
    {
        $userID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $medias = $this->mediaRepository->with([
            'user',
            'favorites',
            'comments.user',
            'downloads'
        ])->latest()->paginate(20);

        $medias->map(function($media) use ($userID) {
            $media->isFavorited = $media->favorites->contains($userID);
            $media->isDownloaded = $media->downloads->contains($userID);
        });

        return response()->json(['data' => $medias]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $userID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $media = $this->mediaRepository->with([
            'user',
            'favorites',
            'comments.user',
            'downloads'
        ])->find($id);

        $media->isFavorited = $media->favorites->contains($userID);
        $media->isDownloaded = $media->downloads->contains($userID);

        return response()->json([
            'data' => $media
        ]);
    }

    public function store(Request $request)
    {


        $user= Auth::guard('api')->user();
        $user = Auth::loginUsingId(1);
        if(!$user) {
            return response()->json(['message'=>'invalid user','success'=>false]);
        }
        if($request->hasFile('media')) {
            $mediaManager = new MediaManager($request->file('media'));
            $uploadedMedia = $mediaManager->storeMedia();
            $mediaType = $uploadedMedia->getMediaType();
            $mediaUploadDir = url(env('MEDIAS_UPLOAD_DIR'));
            $media = $user->medias()->create([
                'large_url' => $mediaUploadDir.$uploadedMedia->largeImagePath,
                'medium_url' => $mediaUploadDir.$uploadedMedia->mediumImagePath,
                'thumb_url' => $mediaUploadDir.$uploadedMedia->thumbnailImagePath,
                'video_url'=> $mediaUploadDir.$uploadedMedia->videoPath,
                'type'=> $uploadedMedia->getMediaType(),
                'caption' => 'asdasd'
            ]);
            $media->load('user');
            return response()->json(['data'=>$media,'success'=>true]);
        }
        return response()->json(['message'=>'is not a file','success'=>false]);
    }

    public function getMediaComments($mediaID)
    {
        $media = $this->mediaRepository->with('user','comments.user')->find($mediaID);
        return response()->json(['data'=>$media]);
    }

    public function getMediaFavorites($mediaID)
    {
        $media = $this->mediaRepository->with('user','comments.user')->find($mediaID);
        return response()->json(['data'=>$media]);
    }

    /**
     * @return mixed
     */
    public function getUploadDir()
    {
        return $this->uploadDir;
    }

}