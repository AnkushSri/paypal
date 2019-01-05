<!DOCTYPE html>
<html>
    <body>

        <div class="greyBar-Heading">
            <div class="container">
                <div class="row">
                    <div class="col-md-12" style="text-align: center;">
                        <h4>PayPal Payment</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="contentWrapper" style="text-align: center;">
            <div class="afterlogin-section viewProfile upload-modal-box add-payment-cards coming-soon-form">
                <span class="total-plan-heading">Amount To Pay</span>
                <span class="total-plan-heading">$10</span>
                <input type="hidden" name="transaction_id" value="<?php echo rand(10,100); ?>">
                <input type="hidden" name="wallet_amount" value="10">
                <input type="hidden" name="currency_code" value="USD">
                <br/>
                <br/>
                <div id="paypal-button"></div>
            </div>
        </div>

        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        <script>
            paypal.Button.render({
            env: 'sandbox', // 'sandbox' or 'production'
            style: {
                layout: 'vertical',  // horizontal | vertical
                size:   'medium',    // medium | large | responsive
                shape:  'rect',      // pill | rect
                color:  'gold'       // gold | blue | silver | black
            },
            commit:true,
            // Set up the payment:
            // 1. Add a payment callback
            payment: function(data, actions) {
            // 2. Make a request to your server
                return paypal.request({
                    method: 'post',
                    url: "create-payment.php",
                    headers: {
                        'x-csrf-token': "",
                    },
                    'data':{
                        'transaction_id': $('input[name="transaction_id"]').val(),
                        'wallet_amount' : $('input[name="wallet_amount"]').val(),
                	}
                }).then(function(data) {
                    return data.id;
                });
            },
            // Execute the payment:
            // 1. Add an onAuthorize callback
            onAuthorize: function(data, actions) {
              // 2. Make a request to your server
                return paypal.request({
                    method: 'post',
                    url: "execute-payment.php",
                    headers: {
                        'x-csrf-token': "",
                    },
                    'data':{
                    		'paymentID'		 : data.paymentID,
                    		'payerID'		 : data.payerID, 
                            'transaction_id' : $('input[name="transaction_id"]').val(),
                            'wallet_amount'  : $('input[name="wallet_amount"]').val()
                    		}
                }).then(function(response) {
                    if(response.status == true){
                    	window.location.href = response.redirect_url;
                    }else{
                    	window.location.href = response.redirect_url;
                    }
                });
            }
          }, '#paypal-button');
        </script>
    </body>
</html>