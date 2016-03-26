<?php

namespace App\Http\Controllers\Auth;

use App\Src\User\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function loginUsingToken(Request $request)
    {
        $user = User::where('api_token',$request->json('api_token'))->first();
        if($user) {
            $user = Auth::loginUsingId($user->id);
            return response()->json(['data'=>$user,'success'=>true],200);
        }

        return response()->json(['success' => false,'message'=>'wrong credentials'], 401);
    }

    public function postLogin(Request $request)
    {
        if (Auth::attempt(([
            'email' => strtolower($request->json('email')),
            'password' => $request->json('password'),
            'active' => 1
        ]),
            true)
        ) {
            $user = Auth::user();
            return response()->json(['success' => true,'data'=>$user],200);
        }

        return response()->json(['success' => false,'message'=>'wrong credentials'], 401);
    }


    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postRegister(Request $request)
    {

        $validator = Validator::make($request->json()->all(), [
            'name' => 'required|alpha',
            'mobile' => 'required|digits:8|numeric|unique:users,mobile|',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|max:255|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors()->all()
            ]);
        } else {
            return response()->json(['success' => true]);
        }

        $user = $this->userRepository->model->create([
            'name' => strtolower($request->json('name')),
            'email' => strtolower($request->json('email')),
            'mobile' => (int)$request->json('mobile'),
            'password' => bcrypt($request->json('password')),
            'active' => 1
        ]);

        try {
//            event(new UserRegistered($user));
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);


        } catch (\Exception $e) {
            $user->delete();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 401);
        }

    }

    /**
     * Confirm the User and Activate
     * Lands on this page When User Clicks the Activation Link in Email
     * @param Request $request
     * @param AuthManager $authManager
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postActivate(Request $request, AuthManager $authManager)
    {
        // If not activated ( errors )
        $code = $request->json('code');
        $email = $request->json('email');
        try {
            $user = $authManager->activateUser($email, $code);
//            event(new UserActivated($user));
        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => 'Could\'nt activate your account. please contact admin'
            ]);
        }

        // redirect to home with active message
        return response()->json(['success' => true, 'message' => 'Account Activated']);

    }

}
