<?php /** @noinspection SpellCheckingInspection */

require 'vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

function isNeedReplaceRate($fileName): bool
{
    $now = time();
    if (!file_exists($fileName)) {
        return true;
    }

    $lastDate = filectime($fileName);
    $diff = $now - $lastDate;
    $minutes = round($diff / 60);
    return $minutes > 2;
}

function replaceRateFromBinanceRate($api, $ticker, $fileName): bool
{
    try {
        /** @var string $price */
        $price = $api->price($ticker);
    } catch (Exception $e) {
        $price = null;
    }

    $fp = fopen($fileName, 'wb');
    if ($price) {
        fwrite($fp, $price);
    }
    fclose($fp);

    return (bool) $price;
}

function main()
{
    $fileName   = $_ENV['FILE_NAME'];
    $apiKey     = $_ENV['API_KEY'];
    $apiSecret  = $_ENV['API_SECRET'];

    $api = new Binance\API($apiKey, $apiSecret);
    if (isNeedReplaceRate($fileName)) {
        replaceRateFromBinanceRate($api, 'DGBUSDT', $fileName);
    }
}

main();
