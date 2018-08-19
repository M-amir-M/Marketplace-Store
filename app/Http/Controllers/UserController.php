<?php

namespace App\Http\Controllers;

use App\Address;
use App\City;
use App\Province;
use App\Role;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Exception;
use phplusir\smsir\Smsir;
use Zizaco\Entrust\Entrust;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $rules = [
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'local' => 'max:30',
            'address' => 'required|max:150',
            'name' => 'required|max:30',
            'type' => 'required|in:admin,brand,p_customer,s_customer',
            'email' => 'email|unique:users',
            'phone' => 'max:15',
            'mobile' => 'required|max:15|unique:users',
            'password' => 'required|min:6',
        ];
        if($request['type'] == 'brand'){
            $rules = [
                'province' => 'required|exists:provinces,id',
                'city' => 'required|exists:cities,id',
                'local' => 'max:30',
                'address' => 'required|max:150',
                'name' => 'required|max:30',
                'type' => 'required|in:admin,brand,p_customer,s_customer',
                'brand' => 'required|max:50',
                'email' => 'email|unique:users',
                'phone' => 'max:15',
                'mobile' => 'required|max:15|unique:users',
                'password' => 'required|min:6',
            ];
        }
        $this->validate($request,$rules);

        $address = Address::create([
            'province_id' => $request['province'],
            'city_id'=> $request['city'],
            'region'=> $request['local'],
            'address'=> $request['address']
        ]);

        $existUser = null;
        $code = '';
        while ($existUser != null){
            $code = rand(00000000,99999999);
            $existUser = User::where(['code' => $code])->first();
        }


        $user = User::create([
            'name' => $request['name'],
            'code' => $code,
            'brand_id' => $request['brand'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'mobile' => $request['mobile'],
            'password' => bcrypt($request['password']),
            'address_id' => $address->id,
            'status' => 1
        ]);

        $role = Role::where(['name' => $request['type']])->first();

        $user->roles()->attach($role->id);

        return $user;
    }

    public function storeName(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:30'
        ]);

        $user = Auth::user();

        $user->update([
            'name' => $request['name'],
        ]);

        return $user;
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:users,id',
            'province' => 'required|exists:provinces,id',
            'city' => 'exists:cities,id',
            'local' => 'required|max:30',
            'address' => 'required|max:150',
            'name' => 'required|max:30',
            'type' => 'required|in:admin,brand,p_customer,s_customer',
            'email' => 'email|unique:users,email,'.$request['id'],
            'phone' => 'max:15',
            'mobile' => 'required|max:15|unique:users,mobile,'.$request['id'],
            'password' => 'required|min:6',
        ];
        if($request['type'] == 'brand'){
            $rules = [
                'id' => 'required|exists:users,id',
                'province' => 'required|exists:provinces,id',
                'city' => 'required|exists:cities,id',
                'local' => 'max:30',
                'address' => 'required|max:150',
                'name' => 'required|max:30',
                'type' => 'required|in:admin,brand,p_customer,s_customer',
                'brand' => 'required|exists:brands,id',
                'email' => 'email|unique:users,email,'.$request['id'],
                'phone' => 'max:15',
                'mobile' => 'required|max:15|unique:users,mobile,'.$request['id'],
                'password' => 'required|min:6',
            ];
        }
        $this->validate($request,$rules);

        $user = User::find($request['id']);
        $address = Address::find($user->address_id);

        $address->update([
            'province_id' => $request['province'],
            'city_id'=> $request['city'],
            'region'=> $request['local'],
            'address'=> $request['address']
        ]);


        $user->update([
            'name' => $request['name'],
            'brand_id' => $request['brand'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'mobile' => $request['mobile'],
            'password' => bcrypt($request['password']),
            'address_id' => $address->id
        ]);

        $role = Role::where(['name' => $request['type']])->first();

        $user->roles()->sync($role->id);

        return $user;
    }

    public function search($name,$page,$role)
    {
        $name = explode('=',$name)[1];
        $page = explode('=',$page)[1];
        $role = explode('=',$role)[1];
//        $articles = Articles::paginate(5, ['*'], 'page', $pageNumber);
//        public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null);

        if($role != ''){
            return User::where('name','like','%'.$name.'%')
                ->whereHas('roles', function ($query)use($role) {
                    $query->where('name', '=', $role);
                })
                ->with('address')
                ->orderBy('created_at', 'desc')
                ->paginate(4,['*'],'page',$page);
        }
        return User::where('name','like','%'.$name.'%')
            ->with('address')
            ->orderBy('created_at', 'desc')
            ->paginate(4,['*'],'page',$page);

    }

    public function getAuth()
    {
        return User::with('address')->find(Auth::user()->id);
    }

    public function getProvinces()
    {
        return response(Province::orderBy('name')->get());
    }

    public function getCities($province_id)
    {
        $cities = City::where(['province_id' => $province_id])->orderBy('name')->get();
        return response($cities);
    }

    public function ProfileUpdate(Request $request)
    {
        $this->validate($request,[
            'id' => 'required|exists:users,id',
            'province' => 'required|exists:provinces,id',
            'city' => 'required|exists:cities,id',
            'local' => 'required|max:30',
            'address' => 'required|max:150',
            'name' => 'required|max:30',
            'email' => 'required|email|unique:users,email,'.$request['id'],
            'phone' => 'required|max:15',
        ]);

        $user = User::find($request['id']);
        $address = Address::find($user->address_id);

        $address->update([
            'province_id' => $request['province'],
            'city_id'=> $request['city'],
            'region'=> $request['local'],
            'address'=> $request['address']
        ]);


        $user->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address_id' => $address->id
        ]);

        return $user;
    }

    public function PasswordUpdate(Request $request)
    {
            $this->validate($request,[
                'new' => 'required|min:6',
            ]);

            $user = Auth::user();

            if(password_verify($request['old'],$user->password)){
                $user->update([
                    'password' => bcrypt($request['new']),
                ]);

                return 'ok';
            }

            return 'error';
    }

    public function resetPassword(Request $request)
    {
        $user = User::whereMobile($request['mobile'])->first();

        $user->verifyToken = random_int(10000, 99999);
        $user->save();

        $text = 'کد تایید حساب شما : ' . $user->verifyToken;

        Smsir::send([$text], [$user->mobile]);

        return response([
            'data' => 'حساب خود را تایید کنید'
        ], 200);

    }

    public function resetPasswordVerify(Request $request)
    {
        $user = User::whereMobile($request['mobile'])->first();

        $this->validate($request,[
                'new' => 'required|min:6',
                'mobile' => 'required|max:15|unique:users,mobile,' . $user->id,
            ]);

        if ($user->verifyToken == $request['verify']) {
            $user->status = 1;
            $user->password = bcrypt($request['new']);
            $user->save();
            if (auth()->attempt(['password' => $request['new'],'mobile'=> $request['mobile']])) {

                return response([
                    'data' => 'خوش آمدید'
                ], 200);
            }
            return response([
                'errors' => [
                    'message' => ['اطلاعات صحیح نیست']
                ]
            ], 403);

        }

        return response([
            'errors' => [
                'message' => ['کد تایید درست نمی باشد']
            ]
        ], 422);
    }

    public function delete(Request $request)
    {
        $user = User::find($request['id']);
        $user->delete();
        return $user;
    }
}
