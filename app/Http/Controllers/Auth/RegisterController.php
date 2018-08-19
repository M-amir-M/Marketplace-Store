<?php

namespace App\Http\Controllers\Auth;

use App\Address;
use App\Role;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use phplusir\smsir\Smsir;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $request)
    {
        $user = User::where(['mobile' => $request['mobile'],'status' => 0])->first();
        $rules = [
            'type' => 'required|in:brand,p_customer,s_customer',
            'mobile' => 'required|max:15|unique:users',
            'password' => 'required|min:6',
        ];
        if($user){
            $rules = [
                'type' => 'required|in:brand,p_customer,s_customer',
                'mobile' => 'required|max:15|unique:users,mobile,'.$user->id,
                'password' => 'required|min:6',
            ];
        }

        return Validator::make($request, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\User
     */
    protected function create(array $request)
    {
        $user = User::create([
            'mobile' => $request['mobile'],
            'code' => uniqid(),
            'type' => $request['type'],
            'password' => bcrypt($request['password']),
            'status' => 0
        ]);
        $user->code = $user->id.mt_rand(100,999);
        $user->save();

        $role = Role::where(['name' => $request['type']])->first();

        $user->attachRole($role);

        return $user;
    }


    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $user = User::where(['mobile' => $request['mobile']])->first();

        if($user == null){
            event(new Registered($user = $this->create($request->all())));

//        $this->guard()->login($user);

            $user->verifyToken = random_int(10000, 99999);
            $user->save();
        }

        $text = 'به بانپ خوش آمدید.کد تایید حساب شما : ' . $user->verifyToken;

        Smsir::send([$text], [$user->mobile]);

        return response([
            'data' => 'حساب خود را تایید کنید'
        ], 200);

    }

    public function verify(Request $request)
    {
        $user = User::whereMobile($request['mobile'])->first();

        $this->validate($request, [
            'type' => 'required|in:brand,p_customer,s_customer',
            'verify' => 'required',
            'mobile' => 'required|max:15|unique:users,mobile,' . $user->id,
        ]);

        if ($user->verifyToken == $request['verify']) {
            $user->status = 1;
            $user->save();
            $this->guard()->login($user);
            return response([
                'data' => 'خوش آمدید'
            ], 200);
        }

        return response([
            'errors' => [
                'message' => ['کد تایید درست نمی باشد']
            ]
        ], 422);

    }

}
