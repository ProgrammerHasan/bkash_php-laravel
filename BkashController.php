<?php
/**
 * Created by PhpStorm
 * User: ProgrammerHasan
 * Date: 31-10-2020
 * Time: 9:16 PM
 */
class BkashController
{
    // grant Token for create payment and other request (***!important) (GET Request)
    public function grantToken()
    {
        $request_token = BkashLiveService::bkashGetToken();
        return response()->json([
            'token_type' => $request_token['token_type'],
            'id_token' => $request_token['id_token'],
            'expires_in' => $request_token['expires_in'],
            'refresh_token' => $request_token['refresh_token'],
        ]);
    }
    // refresh Token (Parameters: $grantRefreshToken) (***) (POST Request)
    public function refreshToken(Request $request)
    {
        $grantRefreshToken = $request->get('grantRefreshToken');
        $request_token = BkashLiveService::bkashRefreshToken($grantRefreshToken);
        // Json Response
        return response()->json([
            'token_type' => $request_token['token_type'],
            'id_token' => $request_token['id_token'],
            'expires_in' => $request_token['expires_in'],
            'refresh_token' => $request_token['refresh_token'],
        ]);
    }
    // Bkash Token Management Completed

    // create Payment (Parameters: $accessToken,$amount, $intent) (***) (POST Request)
    public function createPayment(Request $request): void
    {
        $resultdata = BkashLiveService::createPayment($request->all());
        echo $resultdata;
    }
    // executePayment (Parameters: $accessToken, $paymentID) (***) (POST Request)
    public function executePayment(Request $request): void
    {
        $resultdata = BkashLiveService::executePayment($request->all());
        echo $resultdata;
    }
    // queryPayment (Parameters: $paymentID, $accessToken) (***) (GET Request)
    public function queryPayment(Request $request):void
    {
        $resultdata = BkashLiveService::queryPayment($request->all());
        echo $resultdata;
    }
    // searchTransaction (Parameters: $trxID, $accessToken) (***) (GET Request)
    public function searchTransaction(Request $request):void
    {
        $resultdata = BkashLiveService::searchTransaction($request->all());
        echo $resultdata;
    }

}
