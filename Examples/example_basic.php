<?php
/**
 * @category    Basic Example
 * @package     GoUrl Cryptocurrency Payment API 
 * copyright 	(c) 2014-2015 Delta Consultants
 * @crypto      Supported Cryptocoins -	Bitcoin, Litecoin, Dogecoin, Speedcoin, Darkcoin, Vertcoin, Reddcoin, Feathercoin, Vericoin, Potcoin
 * @website     https://gourl.io/cryptocoin_payment_api.html
 */ 
	require_once( "../cryptobox.class.php" );

	$options = array( 
	"public_key"  => "", 		// place your public key from gourl.io
	"private_key" => "", 		// place your private key from gourl.io
	"webdev_key" => "", 		// optional, gourl affiliate key
	"orderID"     => "your_product1_or_signuppage1_etc", // order name, not unique
	"userID" 	  => "", 		// autogenerate unique identifier for each your user
	"userFormat"  => "COOKIE", 	// save your user identifier userID in cookies
	"amount" 	  => 0,			// convert amountUSD to dogecoin using live exchange rate
	"amountUSD"   => 2,  		// 2 USD
	"period"      => "24 HOUR",	// payment valid period, after 1 day user need to pay again
	"iframeID"    => "",    	// autogenerate iframe html payment box id
	"language" 	  => "EN" 		// english, please contact us and we can add your language	
	);  

	// Initialise Payment Class
	$box1 = new Cryptobox ($options);

	// Display Payment Box or successful payment result   
	$paymentbox = $box1->display_cryptobox();

	// Log
	$message = "";
	
	// A. Process Received Payment
	if ($box1->is_paid()) 
	{ 
		$message .= "A. User will see this message during 24 hours after payment has been made!";
		
		$message .= "<br>".$box1->amount_paid()." ".$box1->coin_label()."  received<br>";
		
		// Your code here to handle a successful cryptocoin payment/captcha verification
		// For example, give user 24 hour access to your member pages
		// Please use also IPN function cryptobox_new_payment($paymentID = 0, $payment_details = array(), $box_status = "") for update db records, etc
		// ...
	}  
	else $message .= "The payment has not been made yet";

	
	// B. One-time Process Received Payment
	if ($box1->is_paid() && !$box1->is_processed()) 
	{
		$message .= "B. User will see this message one time after payment has been made!";	
	
		// Your code here - for example, publish order number for user
		// ...

		// Also you can use $box1->is_confirmed() - return true if payment confirmed 
		// Average transaction confirmation time - 10-20min for 6 confirmations  
		
		// Set Payment Status to Processed
		$box1->set_status_processed(); 
		
		// Optional, cryptobox_reset() will delete cookies/sessions with userID and 
		// new cryptobox with new payment amount will be show after page reload.
		// Cryptobox will recognize user as a new one with new generated userID
		// $box1->cryptobox_reset(); 
	}
	
	
	
	
	

	/*
	 *  IPN - User Instant Payment Notification Function 
	 *  function cryptobox_new_payment($paymentID = 0, $payment_details = array(), $box_status = "")
	 *  
	 *  This user-defined function called every time when a new payment from any user is received successfully.
	 *  For example, send confirmation email, update user membership, etc.
	 *  
	 *  The function will automatically appear for each new payment usually two times : 
	 *  a) when a new payment is received, with values: $box_status = cryptobox_newrecord, $payment_details[confirmed] = 0
	 *  b) and a second time when existing payment is confirmed (6+ confirmations) with values: $box_status = cryptobox_updated, $payment_details[confirmed] = 1.
	 *  
	 *  But sometimes if the payment notification is delayed for 20-30min, the payment/transaction will already be confirmed and the function will
	 *  appear once with values: $box_status = cryptobox_newrecord, $payment_details[confirmed] = 1
	 *  
	 *  If payment received with correct amount, function receive: $payment_details[status] = 'payment_received' and $payment_details[user] = 11, 12, etc (user_id who has made payment)
	 *  If incorrectly paid amount, the system can not recognize user; function receive: $payment_details[status] = 'payment_received_unrecognised' and $payment_details[user] = ''    
	 *
	 *  **** Move this function to the bottom of the file cryptobox.class.php or create a separate file ****
	 *  
	 *  Function gets $paymentID from your table crypto_payments,
	 *  $box_status = 'cryptobox_newrecord' OR 'cryptobox_updated' (description above)
	 *  
	 *  and payment details as array -
	 *  
	 *  1. EXAMPLE - CORRECT PAYMENT - 
	 *  $payment_details = array(
						"status":			"payment_received",
						"err":				"",
						"private_key":		"ZnlH0aD8z3YIkhwOKHjK9GmZl",
						"box":				"7",
						"boxtype":			"paymentbox",
						"order":			"91f7c3edc0f86b5953cf1037796a2439",
						"user":				"115",
						"usercountry":		"USA",
						"amount":			"1097.03916195",
						"amountusd":		"0.2",
						"coinlabel":		"DOGE",
						"coinname":			"dogecoin",
						"addr":				"DBJBibi39M2Zzyk51dJd5EHqdKbDxR11BH",
						"tx":				"309621c28ced8ba348579b152a0dbcfdc90586818e16e526c2590c35f8ac2e08",
						"confirmed":		0,
						"timestamp":		"1420215494",
						"date":				"02 January 2015",
						"datetime":			"2015-01-02 16:18:14"
					);
						
	 *  2. EXAMPLE - INCORRECT PAYMENT/WRONG AMOUNT - 
	 *  $payment_details = array(
						"status":			"payment_received_unrecognised",
						"err":				"An incorrect dogecoin amount has been received",
						"private_key":		"ZnlH0aD8z3YIkhwOKHjK9GmZl",
						"box":				"7",
						"boxtype":			"paymentbox",
						"order":			"",
						"user":				"",
						"usercountry":		"",
						"amount":			"12",
						"amountusd":		"0.002184",
						"coinlabel":		"DOGE",
						"coinname":			"dogecoin",
						"addr":				"DBJBibi39M2Zzyk51dJd5EHqdKbDxR11BH",
						"tx":				"96dadd51287bb7dea904607f7076e8ce121c8428106dd57b403000b0d0a11c6f",
						"confirmed":		0,
						"timestamp":		"1420215388",
						"date":				"02 January 2015",
						"datetime":			"2015-01-02 16:16:28"
					);	
	*/
	function cryptobox_new_payment($paymentID = 0, $payment_details = array(), $box_status = "")
	{

		// Your code here to handle a successful cryptocoin payment/captcha verification
		// for example, send confirmation email to user
		// .... ....
		
		return true;
	}
		
	
	
	
	
?>

<!DOCTYPE html>
<html><head>
<meta http-equiv='cache-control' content='no-cache'>
<meta http-equiv='Expires' content='-1'>
<script src='../cryptobox.min.js' type='text/javascript'></script>
</head>
<body>

<?= $paymentbox ?>
<?= $message ?>
    
</body>
</html>