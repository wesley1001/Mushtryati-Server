<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Src\User\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * CategoryController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(
        UserRepository $userRepository
    ) {
        $this->userRepository = $userRepository;
    }

    public function show($id) {

      $user = $this->userRepository->model->with(['thumbnail','medias'])->find($id);

      return response()->json(['data'=>$user]);

    }

    public function getFavorites($userID)
    {
        $favorites = $this->userRepository->model->with([
            'favorites',
        ])->find($userID);

        return response()->json(['data' => $favorites]);
    }
}
