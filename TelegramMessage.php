<?php
/**
* This class permits a Telegram bot to send messages to chat groups.
*
* Usage is simple:
* $obj = new TelegramMessage("bot_token");
* $obj->send("message", "chat_id");
*
* Useful stuff:
* bot manager: @BotFather
* last updates (e.g. to get chat id): https://api.telegram.org/bot<TOKEN>/getUpdates
* via http://codegists.com/user/mmilidoni
**/
class TelegramMessage {
	
	private $apiUrl = '';
	public function __construct($botToken) {
		$this->apiUrl = 'https://api.telegram.org/bot' . $botToken . '/';
	}
    public function send($messaggio, $chat_id) {
        $continua         = true;
        $tentativiFalliti = 0;
        $maxTentativi     = 5;
        $messaggioErrore  = "";
        for ($i = 0; $i < $maxTentativi && $continua == true; $i++) {
            try {
                $this->apiRequest("sendMessage", array(
                    "chat_id" => $chat_id,
                    "text" => $messaggio
                ));
                $continua = false;
            }
            catch (Exception $e) {
                sleep(5);
                $tentativiFalliti++;
                $messaggioErrore = $e->getMessage();
            }
        }
        
        $f = fopen("telegram_log.txt", "a");
        
        if ($tentativiFalliti == $maxTentativi) {
            fwrite($f, date("Y-m-d H:i:s") . "\t\tTENTATIVO FALLITO\n\t$messaggioErrore\n");
        } else {
            fwrite($f, date("Y-m-d H:i:s") . "\t\tINVIO EFFETTUATO DOPO " . $tentativiFalliti . " TENTATIVI\n");
        }
    }
	
    public function sendContact($telefono, $nome, $chat_id) {
        $continua         = true;
        $tentativiFalliti = 0;
        $maxTentativi     = 5;
        $messaggioErrore  = "";
        for ($i = 0; $i < $maxTentativi && $continua == true; $i++) {
            try {
                $this->apiRequest("sendContact", array(
                    "chat_id" => $chat_id,
                    "first_name" => $nome,
		    "phone_number" => $telefono
                ));
                $continua = false;
            }
            catch (Exception $e) {
                sleep(5);
                $tentativiFalliti++;
                $messaggioErrore = $e->getMessage();
            }
        }
        
        $f = fopen("telegram_log.txt", "a");
        
        if ($tentativiFalliti == $maxTentativi) {
            fwrite($f, date("Y-m-d H:i:s") . "\t\tTENTATIVO FALLITO\n\t$messaggioErrore\n");
        } else {
            fwrite($f, date("Y-m-d H:i:s") . "\t\tINVIO EFFETTUATO DOPO " . $tentativiFalliti . " TENTATIVI\n");
        }
    }	
    
    private function apiRequestWebhook($method, $parameters) {
        if (!is_string($method)) {
            throw new Exception("Method name must be a string\n");
            return false;
        }
        
        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            throw new Exception("Parameters must be an array\n");
            return false;
        }
        
        $parameters["method"] = $method;
        
        header("Content-Type: application/json");
        echo json_encode($parameters);
        return true;
    }
    
    function exec_curl_request($handle) {
        $response = curl_exec($handle);
        
        if ($response === false) {
            $errno = curl_errno($handle);
            $error = curl_error($handle);
            throw new Exception("Curl returned error $errno: $error\n");
            curl_close($handle);
            
            return false;
        }
        
        $http_code = intval(curl_getinfo($handle, CURLINFO_HTTP_CODE));
        curl_close($handle);
        
        if ($http_code >= 500) {
            // do not wat to DDOS server if something goes wrong
            sleep(10);
            return false;
        } else if ($http_code != 200) {
            $response = json_decode($response, true);
            throw new Exception("Request has failed with error {$response['error_code']}: {$response['description']}\n");
            if ($http_code == 401) {
                throw new Exception('Invalid access token provided');
            }
            return false;
        } else {
            $response = json_decode($response, true);
            if (isset($response['description'])) {
                error_log("Request was successfull: {$response['description']}\n");
            }
            $response = $response['result'];
        }
        
        return $response;
    }
    
    function apiRequest($method, $parameters) {
        if (!is_string($method)) {
            throw new Exception("Method name must be a string\n");
            return false;
        }
        
        if (!$parameters) {
            $parameters = array();
        } else if (!is_array($parameters)) {
            throw new Exception("Parameters must be an array\n");
            return false;
        }
        
        foreach ($parameters as $key => &$val) {
            // encoding to JSON array parameters, for example reply_markup
            if (!is_numeric($val) && !is_string($val)) {
                $val = json_encode($val);
            }
        }
        $url = $this->apiUrl . $method . '?' . http_build_query($parameters);
        
        $handle = curl_init($url);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($handle, CURLOPT_TIMEOUT, 60);
        
        return $this->exec_curl_request($handle);
        
    }
}
?>
