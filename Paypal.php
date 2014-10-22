<?php

    namespace BaBeuloula;

    class Paypal {

        private $user = "YOUR_PAYPAL_USER";
        private $password = "YOUR_PAYPAL_PWD";
        private $signature = "YOUR_PAYPAL_SIGNATURE";
        private $endpoint = "https://api-3t.sandbox.paypal.com/nvp";

        public $errors = array();

        public function __construct($user = false, $password = false, $signature = false, $prod = false) {
            if($user) {
                $this->user = $user;
            }
            if($password) {
                $this->password = $password;
            }
            if($signature) {
                $this->signature = $signature;
            }
            if($prod) {
                $this->endpoint = str_replace('sandbox.', '', $this->endpoint);
            }
        }

        public function request($method, $params) {
            $params = array_merge($params, array(
                'METHOD'                        => $method,
                'VERSION'                       => '74.0',
                'USER'                          => $this->user,
                'PWD'                           => $this->password,
                'SIGNATURE'                     => $this->signature
            ));
            $params = http_build_query($params);

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL            => $this->endpoint,
                CURLOPT_POST           => 1,
                CURLOPT_POSTFIELDS     => $params,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_VERBOSE        => 1
            ));

            $response = curl_exec($curl);
            $responseArray = array();
            parse_str($response, $responseArray);

            if (FALSE === $response) {
                $this->errors = "Erreur cURL:<br><br>Erreur n&deg;".curl_errno($curl)."<br><b>".curl_error($curl)."</b>";
                curl_close($curl);
                return false;
            } else {
                if($responseArray['ACK'] == 'Success') {
                    curl_close($curl);
                    return $responseArray;
                } else {
                    $this->errors = var_dump($responseArray);
                    curl_close($curl);
                    return false;
                }
            }
        }
    }

?>
