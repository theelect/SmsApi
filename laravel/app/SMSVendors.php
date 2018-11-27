<?php

namespace App;

class SMSVendors
{
    public static function estore($source= '', $text = '', $phone = '')
	{
		try{

			if(env('APP_ENV') == 'local')
				return 'successful-estore';

			$endpoint 	= "http://www.estoresms.com/smsapi.php?username=neolife&password=Passme@123";
			$endpoint 	.= "&sender={$source}";
			$endpoint	.= "&recipient={$phone}&message={$text}";

			$response = (new \GuzzleHttp\Client())->request('GET', $endpoint);

			return 'successful-estore';

		}catch(Exception $e){

			return $e->getMessage();
		}
		
	}

	public static function routeSMS($source= '', $text = '', $phone = '')
	{
		try{

			if(env('APP_ENV') == 'local')
				return 'successful-rout-sms';

			$endpoint = 	"http://smsplus4.routesms.com/bulksms/bulksms?username=escape1&password=z9m2w4x7";
			$endpoint.= 	"&type=0&destination={$phone}";
			$endpoint.=		"&source={$source}";
			$endpoint.=		"&message={$text}";

			$response = (new \GuzzleHttp\Client())->request('GET', $endpoint);

			return 'successful-route-sms';

		}catch(Exception $e){

			return $e->getMessage();
		}
		
	}

	public static function infoBip($source= '', $text = '', $phone = '')
	{
		try{
			
			if(env('APP_ENV') == 'local')
				return 'successful-infobip';
			
			$client = (new \GuzzleHttp\Client(['base_uri' => 'https://api.infobip.com']));

			$options = [

				'json' => [

					'from' 	=> $source,
					'to'	=> $phone,
					'text'	=> $text
				],
				'headers' => [

					'Accept'		=> 'application/json',
					'Content-Type'	=> 'application/json',
					'Authorization'	=> ['Basic dGhpbmtmaXJzdG5nOmN5bm9zdXJFMDU5MA==']
				]
			];

			$response = $client->post('sms/1/text/single', $options);

			return 'successful-infobip';

		}catch(Exception $e){

			return $e->getMessage();
		}
		
	}
}
