<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Cron extends Cli_Controller {
	public function after_init() {}
    
    public function dragonpay($environment = "local", $is_https = false) {
        $environment_url = $this->get_environment_url($environment);
        $curl_url = ($is_https ? "https://" : "http://") . "{$environment_url}/crawler/dragonpay"; 

        $key = getenv("DRPKEY");
        $curl = curl_init();

        curl_setopt_array($curl, 
            array(
                CURLOPT_URL => $curl_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                    "x-key: {$key}"
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);

        echo $response;
    }
}