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

$debug = "false"; 

if($debug == "true") {
	$caporiga = "<br>";
	$random = rand(91, 100);
		} 
else {
	require_once("TelegramMessage.php");
	$caporiga = "\r\n";
	$random = rand(1, 100);
		}

if ($random > 90) { 

	$url = "https://api.coinmarketcap.com/v1/ticker/?convert=EUR&limit=10";
	$string = file_get_contents($url);
	$results = json_decode($string, true);
//	$satoshi = substr($results[0]['price_btc'],7); 
//	$euri = substr($results[0]['price_eur'],0,7); 

$i=1;
$buffer = "";

$buffer .= "Hey! Crypto Top10 are now: $caporiga $caporiga";

foreach($results as $item) {

$buffer .= $i++." ".$item['name']." (last 24h: ".$item['percent_change_24h']."%) $caporiga ðŸ’¶ ";

$array = explode(".", $item['price_eur']);
	$prima = $array[0];
	$dopo = $array[1];

$buffer .= $prima.",".substr($dopo,0,2); 
	
$buffer .= "â‚¬ ðŸ’µ $";
$buffer .= bcdiv($item['price_usd'],1,2)."";
//$buffer .= "$";

$buffer .= " $caporiga $caporiga";

}


if($debug == "true") {
	echo $buffer;
	}
else {
	$obj2 = new TelegramMessage("123456789:AAAAAAAAAAzzzzzzzzzzzzzzzzzzz"); // IMPORTANTE inserire qui / put here your "bot_token"
	$obj2->send("$buffer", "@chat_id");  // IMPORTANTE inserire qui / put here your "chat_id"
}

					} else { 
							echo "Passo"; 
								}

?>
