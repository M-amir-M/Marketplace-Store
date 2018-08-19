<?php

namespace App\Http\Controllers;

use App\Http\Sadad;
use App\Order;
use App\Orderlist;
use App\Package;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Larabookir\Gateway\Gateway;
use phplusir\smsir\Smsir;

class OrdersController extends Controller
{
    public function orderCart()
    {
        $user = Auth::user();
        return view('orderCart', compact('user'));
    }

    public function comeFromBank(Request $request)
    {
        $key = \Config::get('gateway.sadad.transactionKey');
        $OrderId = $request->OrderId;
        $Token = $request->token;
        $ResCode = $request->ResCode;
        if ($ResCode == 0) {
            $verifyData = array('Token' => $Token, 'SignData' => Sadad::encrypt_pkcs7($Token, $key));
            $str_data = json_encode($verifyData);
            $res = Sadad::CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify', $str_data);
            $arrres = json_decode($res);

            if ($ResCode == 0 && $arrres->ResCode != -1) {
                $factor = Factor::where('trans_id', $OrderId)->first();
                $factor->verify = 1;
                $factor->ref_id = $arrres->RetrivalRefNo;
                $factor->tracking_code = $arrres->SystemTraceNo;
                $factor->save();
                // کارای خودتو انجام میدین اینجا مثلن اگه بخاین تعداد محصول رو کم و زیاد کنید
            } else {
                $tiny = TinyFactor::where('factor_id', $factor->id)->pluck('service_id')->toArray();
                DefaultCost::whereIn('id', $tiny)->increment('booked');
            }

            //Save $arrres->RetrivalRefNo,$arrres->SystemTraceNo,$arrres->OrderId to DataBase
            $message = "شماره سفارش:" . $OrderId . "<br>" . "شماره پیگیری : " . $arrres->SystemTraceNo . "<br>" . "شماره مرجع:" .
                $arrres->RetrivalRefNo . "<br>" . "اطلاعات بالا را جهت پیگیری های بعدی یادداشت نمایید." . "<br>";

            return view('callback', array('trackingCode' => true, 'success' => $message));
        } else
            $message = "تراکنش نا موفق بود در صورت کسر مبلغ از حساب شما حداکثر پس از 72 ساعت مبلغ به حسابتان برمی گردد.";
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $product_ids = $request['idCartProducts'];
        $product_numbers = $request['numberCartProducts'];
        $product_types = $request['types'];
        $orderlist = Orderlist::create([
            'customer_id' => $user->id,
            'shipped_address' => $request['address_id'],
            'description' => $request['description'],
            'price' => '0',
            'status' => 'pending',
        ]);

        $total_price = 0;
        foreach ($product_ids as $key => $product_id) {
            if ($product_types[$key] == 'product') {
                $product = Product::find($product_id);
                Order::create([
                    'product_id' => $product_id,
                    'orderlist_id' => $orderlist->id,
                    'unit_price' => $product->price,
                    'quantity' => $product_numbers[$key],
                    'discount' => 0,
                ]);
                $total_price += $product->price * $request['numberCartProducts'][$key];
            }
            if ($product_types[$key] == 'package') {
                $package = Package::with('products')->find($product_id);
                foreach ($package->products as $product) {
                    Order::create([
                        'product_id' => $product_id,
                        'orderlist_id' => $orderlist->id,
                        'unit_price' => $product->price,
                        'quantity' => $product_numbers[$key],
                        'discount' => 0,
                    ]);
                }
                if (count($package) > 0)
                    $total_price += $package->price*$product_numbers[$key];
            }

        }

        $orderlist->price = $total_price;
        $orderlist->save();

        $transaction = Transaction::create([
            'price' => $total_price,
            'customer_id' => $user->id,
            'orderlist_id' => $orderlist->id,
            'track_code' => 'BNP-' . mt_rand(10000000, 99999999),
            'status' => 0,
        ]);
        if ($request['payment'] == 'online') {
            $url = url('/orders/payment/checker');
            $result = \App\Http\Sadad::goBank($total_price * 10, $transaction->id, $url);
            return $result;
        }
        if ($request['payment'] == 'at_place')
            return response([
                'msg' => 'پرداخت با موفقیت انجام شد.',
                'status' => 'wallet'
            ], 200);


    }

    public function changeStatus(Request $request)
    {
        $orderLists = Orderlist::with('orders')->find($request->id);
        $orderLists->update(['status' => $request->status]);
        return $orderLists;
    }

    public function getOrders()
    {
        $user = Auth::user();

        $orderLists = Orderlist::where(['customer_id' => $user->id])
            ->with(['orders', 'transaction'])
            ->orderBy('created_at', 'desc')
            ->paginate(request('perPage'), ['*'], 'page', request('page'));
        if (Auth::user()->hasRole('admin')) {
            $orderLists = Orderlist::with(['orders', 'transaction', 'address'])
                ->where('status', 'like', '%' . request('status') . '%')
                ->orderBy('created_at', 'desc')
                ->paginate(request('perPage'), ['*'], 'page', request('page'));
            if (\request('province') != '')
                $orderLists = Orderlist::with(['orders', 'transaction', 'address'])
                    ->where('status', 'like', '%' . request('status') . '%')
                    ->whereHas('address', function ($q) {
                        $q->where(['province_id' => \request('province')]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(request('perPage'), ['*'], 'page', request('page'));
            if (\request('city') != '')
                $orderLists = Orderlist::with(['orders', 'transaction', 'address'])
                    ->where('status', 'like', '%' . request('status') . '%')
                    ->whereHas('address', function ($q) {
                        $q->where(['city_id' => \request('city')]);
                    })
                    ->orderBy('created_at', 'desc')
                    ->paginate(request('perPage'), ['*'], 'page', request('page'));
            if (request('user_id') != '') {
                $orderLists = Orderlist::where(['customer_id' => request('user_id')])
                    ->where('status', 'like', '%' . request('status') . '%')
                    ->with(['orders', 'transaction'])
                    ->orderBy('created_at', 'desc')
                    ->paginate(request('perPage'), ['*'], 'page', request('page'));
                if (\request('province') != '')
                    $orderLists = Orderlist::where(['customer_id' => request('user_id')])
                        ->where('status', 'like', '%' . request('status') . '%')
                        ->with(['orders', 'transaction', 'address'])
                        ->whereHas('address', function ($q) {
                            $q->where(['province_id' => \request('province')]);
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(request('perPage'), ['*'], 'page', request('page'));
                if (\request('city') != '')
                    $orderLists = Orderlist::where(['customer_id' => request('user_id')])
                        ->where('status', 'like', '%' . request('status') . '%')
                        ->with(['orders', 'transaction', 'address'])
                        ->whereHas('address', function ($q) {
                            $q->where(['city_id' => \request('city')]);
                        })
                        ->orderBy('created_at', 'desc')
                        ->paginate(request('perPage'), ['*'], 'page', request('page'));
            }
        }
        return $orderLists;
    }

    public function deleteOrder(Request $request)
    {
        $order = Order::find($request['id']);
        $order->delete();
        return $order;
    }

    public function deleteOrderList(Request $request)
    {
        $orderList = Orderlist::find($request['id']);
        $orderList->delete();
        return $orderList;
    }

    public function checker(Request $request)
    {
        $key = \Config::get('gateway.sadad.transactionKey');
        $OrderId = $request->OrderId;
        $Token = $request->token;
        $ResCode = $request->ResCode;
        if ($ResCode == 0) {
            $verifyData = array('Token' => $Token, 'SignData' => Sadad::encrypt_pkcs7($Token, $key));
            $str_data = json_encode($verifyData);
            $res = Sadad::CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Advice/Verify', $str_data);
            $arrres = json_decode($res);

            if ($ResCode == 0 && $arrres->ResCode != -1) {
                $transaction = Transaction::find($OrderId);
                $transaction->status = 1;
                $transaction->track_code = $arrres->RetrivalRefNo;
                $transaction->return_code = $arrres->SystemTraceNo;
                $transaction->save();

                $orders = Order::where('orderlist_id', $transaction->orderlist_id)->get();
                foreach ($orders as $order) {
                    $product = Product::find($order->product_id);
                    $product->update([
                        'quantity' => $product->quantity - $order->quantity
                    ]);
                }

                // کارای خودتو انجام میدین اینجا مثلن اگه بخاین تعداد محصول رو کم و زیاد کنید

                $text = 'از خرید شما متشکریم.سفارشات شما به شماره فاکتور ' . $transaction->orderlist_id . ' در بانپ ثبت شد';

                Smsir::send([$text], [Auth::user()->mobile]);
            }

            //Save $arrres->RetrivalRefNo,$arrres->SystemTraceNo,$arrres->OrderId to DataBase
//            $message = "شماره سفارش:" . $OrderId . "<br>" . "شماره پیگیری : " . $arrres->SystemTraceNo . "<br>" . "شماره مرجع:" .
//                $arrres->RetrivalRefNo . "<br>" . "اطلاعات بالا را جهت پیگیری های بعدی یادداشت نمایید." . "<br>";

            return redirect('/dashboard')->with('paymentS', 'پرداخت با موفقیت انجام شد.سفارش شما ثبت شد.');
        } else
            return redirect('/dashboard')->with('paymentF', 'پرداخت انجام نشد!');

    }

}
