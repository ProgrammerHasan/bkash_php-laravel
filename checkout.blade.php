<form action="" method="post">
@csrf
<div class="row">
    <div class="col-md-12" style="margin-top: -15px;">
        <h4  id="bKash_button" style="display: none!important;">Pay with bKash</h4>
        <button class="btn btn-primary btn-block" disabled id="" type="submit">
            <i class="fa fa-shopping-cart"></i> checkout
        </button>
    </div>
</div>
</form>

{{-- bKash Payment Integration Required --}}
<script src="https://scripts.sandbox.bka.sh/versions/1.2.0-beta/checkout/bKash-checkout-sandbox.js"></script>
@include('bkash-payment-integration')
{{-- End bKash Payment Integration Required --}}
<script>
    //payment Method
    const radioValue = $("input[name='paymentMethodID']:checked").val();
    // bKash
    grantBkashToken();
</script>