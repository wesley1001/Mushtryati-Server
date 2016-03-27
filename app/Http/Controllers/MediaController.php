<?php

namespace App\Http\Controllers;

use App\Src\Comment\Comment;
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

        if($user && $request->photo) {
            if(is_file($request->photo)) {

                $photo =  $request->file('photo');
                return response()->json(['downloaded media'=>$photo]);

                $uploadPath = '/uploads/medias/';
                $storagePath =  public_path().$uploadPath;
                $fileName = rand().'.'.$photo->getClientOriginalExtension();
                $photo->move($storagePath,$fileName);
                $mediaUrl = url($uploadPath.$fileName);
                $media = $user->medias()->create([
                    'url' => $mediaUrl,
                    'type'=>'image',
                    'caption' => 'asdasd'
                ]);
                $media->load('user');
                return response()->json(['data'=>$media,'success'=>true]);
            }
            return response()->json(['message'=>'is not a file','success'=>false]);
        }
        return response()->json(['message'=>'unknown error','success'=>false]);
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

}