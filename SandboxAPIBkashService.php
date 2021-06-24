<?php
/**
 * Created by PhpStorm
 * User: ProgrammerHasan
 * Date: 31-10-2020
 * Time: 9:16 PM
 */
class SandboxAPIBkashService
{
    // bKash config ***secret
    public static function config(): array
    {
        return [
            // CHECKOUT (IFRAME BASED)
            'createURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/create',
            'executeURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/execute/', // must be (/{paymentID)
            'tokenURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/grant',
            'refresh_tokenURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/token/refresh',
            'query_paymentURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/query/', // must be (/{paymentID)
            'search_transactionURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/checkout/payment/search/', // must be (/{trxID})
            // CHECKOUT (URL BASED)
            'tokenized_tokenURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/grant',
            'tokenized_refresh_tokenURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/token/refresh',
            'tokenized_createURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/create',
            'tokenized_executeURL' => 'https://checkout.sandbox.bka.sh/v1.2.0-beta/tokenized/checkout/execute',
            // bKash Script
            'script' => 'https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js',
            // sandbox credential :: formal Sandbox
            'app_key' => '',
            'app_secret' => '',
            'proxy' => '',
            'username' => '',
            'password' => '',
            'token' => '',
        ];
    }
    // get Token (static function)
    public static function bkashGetToken()
    {
        $post_token = array(
            'app_key' => self::config()['app_key'],
            'app_secret' => self::config()['app_secret'],
        );
        $url = curl_init(self::config()['tokenURL']);
        $proxy = self::config()['proxy'];
        $posttoken = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            'password:' . self::config()['password'],
            'username:' . self::config()['username']
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    // refresh Token (static function)
    // ***$grantRefreshToken => Refresh token value found in the Grant Token API against the original id_token.
    public static function bkashRefreshToken($grantRefreshToken)
    {
        $post_token = array(
            'app_key' => self::config()['app_key'],
            'app_secret' => self::config()['app_secret'],
            'refresh_token'=> $grantRefreshToken,
        );
        $url = curl_init(self::config()['refresh_tokenURL']);
        $proxy = self::config()['proxy'];
        $posttoken = json_encode($post_token);
        $header = array(
            'Content-Type:application/json',
            'password:' . self::config()['password'],
            'username:' . self::config()['username']
        );
        curl_setopt($url, CURLOPT_HTTPHEADER, $header);
        curl_setopt($url, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url, CURLOPT_POSTFIELDS, $posttoken);
        curl_setopt($url, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return json_decode($resultdata, true);
    }

    // createPayment
    public static function createPayment($request_data)
    {
        $accessToken = $request_data['accessToken'];
        $amount = $request_data['total_price'];
        $intent = $request_data['intent'];
        $invoice = "Inv".uniqid(); // must be unique
        $proxy = self::config()["proxy"];
        $createpaybody=array('amount'=>$amount, 'currency'=>'BDT', 'merchantInvoiceNumber'=>$invoice,'intent'=>$intent);
        $url = curl_init(self::config()["createURL"]);
        $createpaybodyx = json_encode($createpaybody);
        $header=array(
            'Content-Type:application/json',
            'authorization:'.$accessToken,
            'x-app-key:'. self::config()["app_key"]
        );
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_POSTFIELDS, $createpaybodyx);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdata = curl_exec($url);
        curl_close($url);
        return $resultdata;
    }

    // executePayment
    public static function executePayment($request_data)
    {
        $accessToken = $request_data['accessToken'];
        $paymentID = $request_data['paymentID'];
        $url = curl_init(self::config()["executeURL"].$paymentID);
        $header=array(
            'Content-Type:application/json',
            'authorization:'.$accessToken,
            'x-app-key:'. self::config()["app_key"]
        );
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($url, CURLOPT_PROXY, $proxy);
        $resultdatax=curl_exec($url);
        curl_close($url);
        return $resultdatax;
    }

    // query payment Request Parameters(paymentId,$accessToken)
    public static function queryPayment($request_data)
    {
        $paymentID =$request_data['paymentID'];
        $accessToken = $request_data['accessToken'];
        $url=curl_init(self::config()['query_paymentURL'].$paymentID);
        $header=array(
            'Content-Type:application/json',
            'authorization:'.$accessToken,
            'x-app-key:'.self::config()['app_key']);
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdatax=curl_exec($url);
        curl_close($url);
        return $resultdatax;
    }

    // search transaction Request Parameters(trxID,$accessToken)
    public static function searchTransaction($request_data)
    {
        $trxID =$request_data['trxID'];
        $accessToken = $request_data['accessToken'];
        $url=curl_init(self::config()['search_transactionURL'].$trxID);
        $header=array(
            'Content-Type:application/json',
            'authorization:'.$accessToken,
            'x-app-key:'.self::config()['app_key']);
        curl_setopt($url,CURLOPT_HTTPHEADER, $header);
        curl_setopt($url,CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($url,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($url,CURLOPT_FOLLOWLOCATION, 1);
        $resultdatax=curl_exec($url);
        curl_close($url);
        return $resultdatax;
    }
}
