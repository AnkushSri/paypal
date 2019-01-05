<?php 
define('PAYPAL_CLIENT_ID', '');
define('PAYPAL_SECRET', '');

//Check PayPal mode, and change PayPal url according for Sandbox or Live.
$PayPal_BASE_URL = 'https://api.sandbox.paypal.com/v1/';

// request http using curl
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $PayPal_BASE_URL . 'oauth2/token');
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_USERPWD, PAYPAL_CLIENT_ID . ":" . PAYPAL_SECRET);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
$result = curl_exec($ch);            

$json = json_decode($result);

$accessToken = $json->access_token;

$amount = $_POST['wallet_amount'];
$transaction_id = $_POST['transaction_id'];
$currency_code = 'USD';

if (empty($result)) {
    return FALSE;
} else {
    $json = json_decode($result);
    $price_to_pay = $amount;
    $accessToken = $json->access_token;

    $random_string = $transaction_id;

    $curl = curl_init();
    $data = '{
	            "intent":"sale",
	            "redirect_urls":{
	                "return_url":"payment-success.php",
	                "cancel_url":"payment-cancel.php"
	            },
	            "payer":{
	                "payment_method":"paypal"
	            },
	            "application_context": {
	                "shipping_preference": "NO_SHIPPING"
	            },
	            "transactions":[
	            {
	                "amount":{
	                    "total":"'.$price_to_pay.'",
	                    "currency":"'.$currency_code.'"
	            },
	            "invoice_number": "INV-'.$random_string.'",
	            "description":"For wallet payment."
	            }
	          ]
	        }';

    curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL . 'payments/payment');
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
      "Content-Type: application/json",
      "Authorization: Bearer ".$accessToken, 
      "Content-length: ".strlen($data))
    );

    $response1 = curl_exec($curl);
    echo $response1;
}
?>