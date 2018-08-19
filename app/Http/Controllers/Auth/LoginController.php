<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    public function username()
    {
        return 'mobile';
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        // Validation Data
        $validData = $this->validate($request, [
            'mobile' => 'required|exists:users,mobile',
            'password' => 'required'
        ]);

        $user =User::whereMobile($request['mobile'])->first();

        // Check Login User
        if($user->status == 1) {
            if (!auth()->attempt($validData)) {
                return response([
                    'errors' => [
                        'message' => ['اطلاعات صحیح نیست']
                    ]
                ], 403);
            }

            return response([
                'auth' => Auth::check(),
                'status' => 'ok'
            ], 200);
        }
        return response([
            'errors' => [
                'message' => ['حساب شما فعال نشده است.']
            ]
        ], 403);
    }

}
