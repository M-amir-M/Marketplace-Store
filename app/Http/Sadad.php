<?php
/**
 * Created by PhpStorm.
 * User: M.amir.M
 * Date: 2/12/2018
 * Time: 3:27 PM
 */

namespace App\Http;


class Sadad
{
    public static function encrypt_pkcs7($str, $key)
    {
        $key = base64_decode($key);
        $block = @mcrypt_get_block_size("tripledes", "ecb");
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $ciphertext = @mcrypt_encrypt("tripledes", $key, $str,"ecb");
        return base64_encode($ciphertext);
    }
//Send Data
    public static function CallAPI($url, $data = false)
    {
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json','Content-Length: ' . strlen($data)));
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    public static function goBank($amount , $orederId , $callback)
    {
        $key= \Config::get('gateway.sadad.transactionKey');
        $MerchantId= \Config::get('gateway.sadad.merchant');
        $TerminalId= \Config::get('gateway.sadad.terminalId');
        $Amount= $amount; //Rials
        $OrderId= $orederId;
        $LocalDateTime=date("m/d/Y g:i:s a");
        $ReturnUrl= $callback;
        $SignData=self::encrypt_pkcs7("$TerminalId;$OrderId;$Amount","$key");
        $data = array('TerminalId'=>$TerminalId,
            'MerchantId'=>$MerchantId,
            'Amount'=>$Amount,
            'SignData'=> $SignData,
            'ReturnUrl'=>$ReturnUrl,
            'LocalDateTime'=>$LocalDateTime,
            'OrderId'=>$OrderId);
        $str_data = json_encode($data);
        $res=self::CallAPI('https://sadad.shaparak.ir/vpg/api/v0/Request/PaymentRequest',$str_data);
        $arrres=json_decode($res);
        if($arrres->ResCode==0)
        {
            $Token= $arrres->Token;
            $url="https://sadad.shaparak.ir/VPG/Purchase?Token=$Token";
            return response([
                'link' => $url,
                'status' => 'online'
            ], 200);
        }
        else
            return response([
                'errors' => [
                    'message' => ['مشکلی در پرداخت وجود دارد!']
                ]
            ], 422);
    }
}