<?php
//Check PayPal mode, and change PayPal url according for Sandbox or Live.
define('PAYPAL_CLIENT_ID', 'ASyCFYb2d35Bs8OkoMrMjGKTWXSic7Y-xEOFoZ5S7WSkmuIyiGO6hc6UKKEhsiX_Njc308xHRJWPWJmf');
define('PAYPAL_SECRET', 'EHuzSULe1AfNq_gzTkzxg0kn2sMshTN1IMLRmkieCPRsPG6qdmQVntNHh4hSe2gOzQems99pRc_1Od4U');

//Check PayPal mode, and change PayPal url according for Sandbox or Live.
$PayPal_BASE_URL = 'https://api.sandbox.paypal.com/v1/';

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

$paymentID = $_POST['paymentID'];
$payerID   = $_POST['payerID'];

$transaction_id = $_POST['transaction_id'];
$amount = $_POST['wallet_amount'];
$currency_code = 'USD';

$curl = curl_init();
$data = '{
          "payer_id":"'.$payerID.'",
          "transactions":[
            {
              "amount":{
                "total":"'.$amount.'",
                "currency":"'.$currency_code.'"
              }
            }
          ]
        }';

curl_setopt($curl, CURLOPT_URL, $PayPal_BASE_URL.'payments/payment/'.$paymentID.'/execute/');
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
  "Content-Type: application/json",
  "Authorization: Bearer ".$accessToken) 
);

$response2 = curl_exec($curl);

//make arr
$response_arr = (array)json_decode(json_encode(json_decode($response2), 128));

//check if payment is completed
if(!empty($response_arr['transactions'])){
    if($response_arr['transactions'][0]->related_resources[0]->sale->state == 'completed'){
        
        // update job status
        $saleID = $response_arr['transactions'][0]->related_resources[0]->sale->id;

        $status = true;
        $redirect_url = 'payment-success.php';

    }else{
        $status = false;
        $redirect_url = 'payment-fail.php';
    }
}else{
    $status = false;
    $redirect_url = 'payment-fail.php';
}

echo json_encode([
    'status'       => $status,
    'redirect_url' => $redirect_url,
]);
?>