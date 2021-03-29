<?php

//https://www.mercadobitcoin.com.br/api-doc/

class MercadoBitcoin {

    protected $apiId = null;
    protected $apiKey = null;

    protected $urlBase = "https://www.mercadobitcoin.net";

    public function __construct($apiId = "", $apiKey = "") {
        $this->apiKey = $apiKey;
        $this->apiId = $apiId;
    }

    public function ticker($currency = 'BTC') {
        $apiURL = "/{$currency}/ticker";
        return $this->initCurl($apiURL);
    }

    public function bookOrders($currency = 'BTC') {
        $apiURL = "/{$currency}/orderbook";
        return $this->initCurl($apiURL);
    }

    public function trades(
    $currency = 'BTC', $hours = 24, $since = ""
    ) {
        $timeZone = new \DateTimeZone('America/Sao_Paulo');

        $start_time = new \DateTime('now');
        $start_time->format(\DateTime::ATOM);
        $start_time->setTimezone($timeZone);
        $start_time->modify('-' . $hours . ' hour');
        $start_time = $start_time->getTimestamp();

        $end_time = new \DateTime('now');
        $end_time->format(\DateTime::ATOM);
        $end_time->setTimezone($timeZone);
        $end_time = $end_time->getTimestamp();

        if (!empty($since)) {
            $apiURL = "/{$currency}/trades?since={$since}";
        } else {
            $apiURL = "/{$currency}/trades/?{$start_time}/{$end_time}";
        }

        return $this->initCurl($apiURL);
    }

    public function summary($currency = 'BTC') {
        $apiURL = "/{$currency}/day-summary/" . date('Y/m/d', strtotime("-1 days"));
        return $this->initCurl($apiURL);
    }

    private function initCurl($url = '', $apiKeyRequired = false, $fields = [], $method = 'GET') {

        $curl = curl_init();

        $header = [
            'Content-Type: application/json'
        ];

        $apiPath = '/api';

        $postFields = '';
        if ($apiKeyRequired) {

            $header = [
                'Content-Type: application/x-www-form-urlencoded'
            ];

            $apiPath = '/tapi/v3/';

            foreach (array_keys($fields) as $key) {

                if (is_array($fields[$key])) {
                    $fields[$key] = json_encode($fields[$key]);
                } else {
                    $fields[$key] = urlencode($fields[$key]);
                }
            }

            $postFields = http_build_query($fields);

            $message = $apiPath . "?" . $postFields;

            $header[] = "TAPI-ID:" . $this->apiId;
            $header[] = "TAPI-MAC:" . $this->signMessage($message);
        }

        $options = [
            CURLOPT_URL => $this->urlBase . $apiPath . $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $header,
			
			CURLOPT_SSL_VERIFYPEER => false
        ];

        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);

        $err = curl_error($curl);
        curl_close($curl);

        ob_start();
        print_r(date('Y-m-d H:i:s') . ' = ' . $response);
        $sTXT = ob_get_contents();
        $hArq = fopen('logs/logs.txt', 'a+');
        fwrite($hArq, $sTXT . "\n\n");
        fclose($hArq);
        ob_end_clean();

        return $err ? "cURL Error #: {$err}" : json_decode($response);
    }

    private function signMessage($message) {
        $signedMessage = hash_hmac('sha512', $message, $this->apiKey);
        return $signedMessage;
    }

    public function listOrders($coin_pair = 'BRLBTC', $has_fills = "",  $from_timestamp = "", $to_timestamp  = "", $status_list = array()) {

        $tapi_method = 'list_orders';
        
        $fields = compact('tapi_method', 'coin_pair', 'has_fills', 'from_timestamp', 'to_timestamp', 'status_list');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function listSystemMessages($level = 'INFO') {

        $tapi_method = 'list_system_messages';
        
        $fields = compact('tapi_method', 'level');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function getAccountInfo() {

        $tapi_method = 'get_account_info';
        
        $fields = compact('tapi_method');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function getOrder($order_id = 0, $coin_pair = 'BRLBTC') {

        $tapi_method = 'get_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'order_id');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function listOrderbook($coin_pair = 'BRLBTC') {

        $tapi_method = 'list_orderbook';
        
        $fields = compact('tapi_method', 'coin_pair');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function placeBuyOrder($coin_pair = 'BRLBTC', $quantity = 0, $limit_price = 0) {

        $tapi_method = 'place_buy_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'quantity', 'limit_price');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function placeSellOrder($coin_pair = 'BRLBTC', $quantity = 0, $limit_price = 0) {

        $tapi_method = 'place_sell_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'quantity', 'limit_price');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function placeMarketBuyOrder($coin_pair = 'BRLBTC', $cost = 0) {

        $tapi_method = 'place_market_buy_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'cost');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function placeMarketSellOrder($coin_pair = 'BRLBTC', $quantity = 0) {

        $tapi_method = 'place_market_sell_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'quantity');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function cancelOrder($coin_pair = 'BRLBTC', $order_id = "") {

        $tapi_method = 'cancel_order';
        
        $fields = compact('tapi_method', 'coin_pair', 'order_id');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function getWithdrawal($coin = 'BTC', $withdrawal_id = "") {

        $tapi_method = 'get_withdrawal';
        
        $fields = compact('tapi_method', 'coin', 'withdrawal_id');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

    public function withdrawCoin($coin = 'BTC', $description = "", $address = "", $quantity = 0, $tx_fee = 0, $tx_aggregate = "", $via_blockchain = "false") {

        $tapi_method = 'withdraw_coin';

        $fields = compact('tapi_method', 'coin', 'description', 'address', 'quantity', 'tx_fee', 'tx_aggregate', 'via_blockchain');

        $apiURL = '';
        $apiKeyRequired = true;

        return $this->initCurl($apiURL, $apiKeyRequired, $fields, 'POST');
    }

}
