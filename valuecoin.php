<?php
/**
* Mandatory:
* bot_token 
* chat_id 
* https://github.com/drPietro
* 
* Useful stuff:
* bot manager: @BotFather
* last updates (e.g. to get chat id): https://api.telegram.org/bot<TOKEN>/getUpdates
**/

$debug = true;
if ($debug == true) { 
	$caporiga = "<br>"; 
	$timing = 1; // eseguito ogni volta
		} else { 
	//$caporiga = "\r\n"; 
	$timing = 90; // eseguito il 10% delle volte
			}

require_once("TelegramMessage.php");

/****************************************** REQUIRED */
$coin_name = "stellar";
$coin_tld = "xlm";
// 0.00000318 BTC this format is ugly, so better use satoshi ;)
$coin_range_satoshi = 7;
/* end REQUIRED */


$random = rand(1, 100);
if ($random > $timing) { 

	$url = "https://api.coinmarketcap.com/v1/ticker/".$coin_name."/?convert=EUR";
	$string = file_get_contents($url);
	$results = json_decode($string, true);

	$satoshi = substr($results[0]['price_btc'],$coin_range_satoshi); 
	$euri = substr($results[0]['price_eur'],0,7); 
	$diff_24h = $results[0]['percent_change_24h'];

	$url_BTC = "https://api.coinmarketcap.com/v1/ticker/bitcoin/?convert=EUR";
	$string_BTC = file_get_contents($url_BTC);
	$results_BTC = json_decode($string_BTC, true);

	$BTC_diff_24h = $results_BTC[0]['percent_change_24h'];
	$BTC_Value = substr($results_BTC[0]['price_eur'],0,7); 

		$array = explode(".", $BTC_Value);
			$prima = $array[0];
			$dopo = $array[1];
		$BTC_Value = $prima.",".substr($dopo,0,2); 


$Bittrex_Data = file_get_contents("https://bittrex.com/api/v1.1/public/getmarketsummary?market=btc-".$coin_tld."");
$Bittrex_Json = json_decode($Bittrex_Data, true);
$Bittrex_Val_High = number_format($Bittrex_Json["result"][0]["High"], 8, '.', '');
$Bittrex_Satoshi_High = substr($Bittrex_Val_High,$coin_range_satoshi); 
$Bittrex_Val_Low = number_format($Bittrex_Json["result"][0]["Low"], 8, '.', '');
$Bittrex_Satoshi_Low = substr($Bittrex_Val_Low,$coin_range_satoshi); 

$Poloniex_Data = file_get_contents("https://poloniex.com/public?command=returnTicker");
$Poloniex_Json = json_decode($Poloniex_Data, true);
$Poloniex_Val_High = $Poloniex_Json["BTC_SC"]["high24hr"];
$Poloniex_Satoshi_High = substr($Poloniex_Val_High,$coin_range_satoshi); 
$Poloniex_Val_Low = $Poloniex_Json["BTC_SC"]["low24hr"];
$Poloniex_Satoshi_Low = substr($Poloniex_Val_Low,$coin_range_satoshi); 

$text = "Hey! ".strtoupper($coin_tld)." vale ora $satoshi satoshi (last 24h: $diff_24h%) $caporiga
Bittrex: high $Bittrex_Satoshi_High low $Bittrex_Satoshi_Low $caporiga
Poloniex: high $Poloniex_Satoshi_High low $Poloniex_Satoshi_Low $caporiga
BTC vale $BTC_Value euro ($BTC_diff_24h%)";


if($debug == "true") {
	echo $text;
	}
else {
	$obj = new TelegramMessage("123456789:AAAAAAAAAAzzzzzzzzzzzzzzzzzzz"); // IMPORTANTE inserire qui / put here your "bot_token"
	$obj->send("$text", "@chat_id"); // IMPORTANTE inserire qui / put here your "chat_id"
}
				} else { 
						echo "Passo"; 
								}
?>