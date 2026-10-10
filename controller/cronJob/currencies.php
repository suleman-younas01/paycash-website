<?php
require("config/env.php");
// API endpoint for Coinbase exchange rates (provides cryptocurrencies)
$apiUrl = "https://api.coinbase.com/v2/exchange-rates";
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);
if (isset($data['data']['rates'])) {
    $rates = $data['data']['rates'];
    foreach ($rates as $currency => $rate) {
        // Check if currency exists in the database
        $exists = $h->table('cryptocurrencies')->select()->where('currency_code', '=',$currency)->fetchAll();
        if ($exists) {
            $update = $h->update('cryptocurrencies')->values(['exchange_rate' => $rate])->where('currency_code','=',$currency)->run();
        } else {
            $res= $h->table('cryptocurrencies')->insertOne(['currency_code' => $currency,'exchange_rate' => $rate]);
        }
    }
    echo "Cryptocurrencies updated successfully.\n";
} else {
    echo "Failed to fetch cryptocurrencies.\n";
}
?>