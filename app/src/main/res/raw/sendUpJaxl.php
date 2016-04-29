<?php
include_once 'C:\Users\Meer\JAXL\jaxl.php';//to use JAXL librabry 

$client = new JAXL(array(
    'jid' => '1061134656358@gcm.googleapis.com',
    'pass' => 'AIzaSyAKT0STg3C4VaKH5ygLRK07OcDDrzL8ibU',
    'host' => 'gcm-preprod.googleapis.com',
    'port' => 5236,
   'strict' => false,
    'force_tls' => true,
    'log_level' => JAXL_DEBUG,
    'auth_type' => 'PLAIN',
    'protocol' => 'tls',
     'ssl' => TRUE,
    'log_path'=> 'myUpstreamlog.txt'  /*This create text file to comminication between gcm and your server*/
));

$client->add_cb('on_message_stanza', function($msg) {
 echo 'now what!!';
 $myfile = fopen("on_message_stanza.txt", "w");
fwrite($myfile, "on_message_stanza");
fclose($myfile);
 });

 $client->add_cb('on_auth_success', function() {
// echo 'it should';
//Here is for sending downstream msg

	//Getting registration token we have to make it as array 
	$reg_token = array('fy6HF-kKO3M:APA91bGO3F0BKHk6nfPpwf4iLJAZgLag2ZL7uRyRC2vHyE_hmgRCaaj2E5PbhobN0ki7_rfEfOyUjD9-5ml064mULKynalDt69G1FmY_k2CnalMRe-eFzUswPjUrx5yxCZOUfI3tsFSc');
	

	
	//Creating a message array 
	$msg = array
	(
		'hello meer this is your server' 

	);
	
	//Creating a new array fileds and adding the msg array and registration token array here 
	$fields = array
	(
		'to' 	=> $reg_token,
		'message_id' => 1,
		'data'			=> $msg,
		'time_to_live' => 600 ,
      'delay_while_idle'=> true,
      'delivery_receipt_requested' => true
	);
	

	
	//Using curl to perform http request 
	$ch = curl_init();
	curl_setopt( $ch,CURLOPT_URL, 'https://android.googleapis.com/gcm/send' );
	curl_setopt( $ch,CURLOPT_POST, true );

	curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
	curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );
	
	//Getting the result 
	$result = curl_exec($ch );
	curl_close( $ch );
	
	//Decoding json from result 
	$res = json_decode($result);
$myfile = fopen("sendAcktoClient.txt", "w");
fwrite($myfile, $res);
fclose($myfile);
  }); 

 $client->add_cb('on_error_message',function()
 {
 global $client;
 echo 'error<br/>';
 _info('got on_error_message cb jid'.$client->full_jid->to_string());
 });

$client->start();

?>