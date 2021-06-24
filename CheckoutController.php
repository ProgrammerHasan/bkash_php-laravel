<?php

class CheckoutController
{
    public function checkoutConfirm(Request $request)
    {
        $paymentID = $request->get('paymentId');
        $queryPayment = BkashLiveService::queryPayment($paymentID);
        if($queryPayment['transactionStatus'] == 'Completed')
        {
           // then write your code

            toastr()->success('Congratulations! Your Payment is Success!');
            return redirect(route('success.payment'));
        }
        toastr()->error('Your Payment is cancelled!');
        return redirect()->back();
    }
}
