<?php

function get_creds($basicLogin,$basicPass, $data, $url) {


	$streamopt = array(
		'ssl' => array(
			'verify-peer' => false, 
			), 
		'http' => array(
			'method' => 'POST', 
			'ignore_errors' => false, 
			'header' => array(
				'Authorization: Basic ' . base64_encode( $basicLogin . ':' . $basicPass), 
				'Content-Type: application/json', 
				'Accept: application/json, */*; q=0.01',
			), 
			'content' => myJson_encode($data), 
		), 
	);

	$streamparams = array();

	$context = stream_context_create($streamopt);

	$stream = fopen($url . 'Basic/request'.'?'.http_build_query($streamparams,'','&'), 'rb', false, $context);

	$return_code = @explode(' ', $http_response_header[0]);
    $return_code = (int)$return_code[1];

	switch($return_code){
        case 200:
            $ret = stream_get_contents($stream);
			$meta = stream_get_meta_data($stream);

			if ($ret) {
				$ret = json_decode($ret, TRUE);
			}
            break;
        	default: //error
            $ret = NULL;
			$meta = $return_code;
            break;
    }


	return array(
		'contents'=> $ret, 
		'metadata'=> $meta
	);
}

function myJson_encode($str)
{
	return str_replace('\\/', '/',json_encode($str));
}


//hard coded test data
$basicLogin = '6b720f5866b53c137567e98db8d433641cc542d8';
$basicPass = '8775234895c56d5b8b59dfcda4e439593edb572a';
$url = 'http://localhost/learninglocker/public/data/xAPI/';
$data = array(
	'scope' => array ('all'),
	'expiry' => '9999-01-01T00:00:00.000+00:00',
	'historical' => FALSE,
	'actors' => array(
		array(
			"objectType"=> "Agent",
        	"mbox"=> "mailto:example@example.com",
       	 	"name"=> "Example Example"
		)
	),
	'auth' => array(
		"objectType"=> "Agent",
        "mbox"=> "mailto:example@example.com",
        "name"=> "Example Example"
	),
	'activity' => array(
		array(
			"id"=> "http://example.com/activity",
		)
	),
	'registration' => '69b685c0-e83f-11e3-ac10-0800200c9a66'
);
//end hard coded test data

//echo out the auth property for Fiddler!
echo 'Authorization: Basic ' . base64_encode( $basicLogin . ':' . $basicPass);

//Call the function to get the credentials from the LRS
$creds = get_creds($basicLogin,$basicPass, $data, $url) ;

$key = $creds['contents']['key']; 
$secret = $creds['contents']['secret'];

?>

<p><b>Key:</b><?php echo $key ?></p>
<p><b>Secret:</b><?php echo $secret ?></p>


