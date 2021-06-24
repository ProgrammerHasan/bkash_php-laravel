<script type="text/javascript">
    window.Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        onOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    {{--Grant Token
    Token creation for accessing bKash payment APIs.--}}
    let accessToken='';
    let grantRefreshToken='';
    let newRefreshToken='';
    let accessTokenExpiresIn='';
    let bKashTrxID ='';
    let paymentID='';
    function grantBkashToken()
    {
        Toast.fire({
            icon: 'info',
            title: 'Processing ...'
        });
        $.ajax({
            url: "/bkash/checkout/token/grant",
            type: 'get',
            contentType: 'application/json',
            success: function (data) {
                // console.log(JSON.stringify(data));
                accessToken=data.id_token;
                accessTokenExpiresIn=data.expires_in;
                grantRefreshToken=data.refresh_token;
                clickPayButton();
            },
            error: function(){
                console.log('error');
            }
        });
    }
    {{--Refresh Token--}}
    function refreshToken(grantRefreshToken)
    {
        $.ajax({
            url: "/bkash/checkout/token/refresh",
            type: 'POST',
            data: {grantRefreshToken:grantRefreshToken},
            success: function (data) {
                console.log('got data from token  ..');
                console.log(JSON.stringify(data));
                accessToken=data.id_token;
                accessTokenExpiresIn=data.expires_in;
                newRefreshToken=data.refresh_token;
            },
            error: function(){
                console.log('error');
            }
        });
    }
    {{--Bkash Token Management Completed--}}
    {{--finally createCheckout && executeCheckout--}}
        $(document).ready(function(){
            const paymentConfig = {
                createCheckoutURL: "https://your_domain.com/bkash/checkout/payment/create",
                executeCheckoutURL: "https://your_domain.com/bkash/checkout/payment/execute",
            };
            let total_amount = $('#totalAmount').val();
            // let total_amount = 12;
            let paymentRequest;
            paymentRequest = { amount:total_amount, intent:'sale'};
            // console.log(JSON.stringify(paymentRequest));
            // start bKash init
            bKash.init({
                paymentMode: 'checkout',
                paymentRequest: paymentRequest,

                // createRequest
                createRequest: function(request){
                    console.log('=> createRequest (request) :: ');
                    Toast.fire({
                        icon: 'warning',
                        title: 'Create Request ...'
                    });
                    $.ajax({
                        url: paymentConfig.createCheckoutURL,
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({"total_price": paymentRequest.amount, "intent": paymentRequest.intent, "accessToken": accessToken}),
                        success: function (data) {
                            console.log('got data from create  ..');
                            console.log(JSON.stringify(data));
                            const obj = JSON.parse(data);
                            if(data && obj.paymentID != null){
                                paymentID = obj.paymentID;
                                bKash.create().onSuccess(obj);
                            }
                            else {
                                console.log('error');
                                bKash.create().onError();
                            }
                        },
                        error: function(){
                            console.log('error');
                            bKash.create().onError();
                        }
                    });
                },
                // executeRequest
                executeRequestOnAuthorization: function(){
                    console.log('=> executeRequestOnAuthorization');
                    $.ajax({
                        url: paymentConfig.executeCheckoutURL,
                        type: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({"paymentID": paymentID, "accessToken": accessToken}),
                        success: function (data) {
                            console.log('got data from execute  ..');
                            console.log(JSON.stringify(data));
                            // alert("Execute Payment response found");
                            const dataParse = JSON.parse(data);
                            if (data && dataParse.paymentID != null) {
                                Swal.fire(
                                    'Congratulations!',
                                    'Your Payment is Success',
                                    'success'
                                )
                                alert('[Success] data : ' + JSON.stringify(data));
                                bKashTrxID = dataParse.trxID;
                                //*****
                                // get your paymentId and bKashTrxId and Then Submit your form

                            }else if(dataParse.errorCode == 2029){
                                Swal.fire(
                                    'Payment failed!',
                                    'Duplicate for All Transactions',
                                    'error'
                                )
                                bKash.execute().onError();//run clean up code
                                setTimeout(function () {
                                    location.reload();
                                },8000);
                            }else if(dataParse.errorCode == 2017){
                                Swal.fire(
                                    'Payment failed!',
                                    'Wrong verification limit exceeded',
                                    'error'
                                )
                                bKash.execute().onError();//run clean up code
                                setTimeout(function () {
                                    location.reload();
                                },6000);
                            }else if(dataParse.errorCode == 2023){
                                Swal.fire(
                                    'Payment failed!',
                                    'Insufficient Balance',
                                    'error'
                                )
                                bKash.execute().onError();//run clean up code
                                setTimeout(function () {
                                    location.reload();
                                },6000);
                            }else {
                                alert('[ERROR] data : ' + JSON.stringify(data));
                                alert('Unsuccessful Checkout Payment');
                                location.reload();
                                bKash.execute().onError();//run clean up code
                            }
                        },
                        error: function () {
                            alert('An alert has occured during execute');
                            bKash.execute().onError();//run clean up code
                        }
                    });
               },
             // end executeRequest
                onClose: function () {
                    alert('User has clicked the close button');
                    location.reload();
                }
            });
        // end bKash init
        });

    function callReconfigure(val){
        bKash.reconfigure(val);
    }
    function clickPayButton(){
        $("#bKash_button").trigger('click');
    }

    // other bkash api
    function queryPayment(queryPaymentResult)
    {
        $.ajax({
            url: "/bkash/checkout/payment/search?trxID="+bKashTrxID+"&accessToken="+accessToken,
            type: 'get',
            contentType: 'application/json',
            success: function (data) {
                console.log(JSON.stringify(data));
                queryPaymentResult = data;
            },
            error: function(){
                console.log('error');
            }
        });
    }
    function searchTransaction(searchTransactionResult)
    {
        $.ajax({
            url: "/bkash/checkout/payment/query?paymentID="+paymentID+"&accessToken="+accessToken,
            type: 'get',
            contentType: 'application/json',
            success: function (data) {
                console.log(JSON.stringify(data));
                searchTransactionResult = data;
            },
            error: function(){
                console.log('error');
            }
        });
    }
</script>
