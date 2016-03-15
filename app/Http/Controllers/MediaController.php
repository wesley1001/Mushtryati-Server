<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Src\Media\Media;
use Illuminate\Support\Facades\Auth;

class MediaController extends Controller
{
    /**
     * @var Media
     */
    private $mediaRepository;

    /**
     * MediaController constructor.
     * @param Media $mediaRepository
     */
    public function __construct(Media $mediaRepository)
    {
        $this->mediaRepository = $mediaRepository;
    }

    // Get Medias
    public function index()
    {
        $userID = Auth::guard('api')->user() ? Auth::guard('api')->user()->id  :'0';
        $medias = $this->mediaRepository->with([
            'user',
            'favorites'
        ])->latest()->paginate(20);

        $medias->map(function($media) use ($userID) {
            if ($media->favorites->contains($userID)) {
                $media->isFavorited = true;
            } else {
                $media->isFavorited = false;
            }
        });

        return response()->json(['data' => $medias]);
    }

    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function show($id)
    {
        $media = $this->mediaRepository->model->with([
            'user',
            'comments.user'
        ])->find($id);

        return response()->json([
            'data' => $media
        ]);

    }


}