<?php
/*
    Gatey SDK for PHP

    Made for Florgon, by @MrCheatEugene on github
*/

class GateyAuthException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class GateyAPIException extends Exception
{
    public function __construct($message, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}

class GateySDK 
{
    protected $id;
    protected $client_secret;
    protected $server_secret;
    protected $endpoint;

    public function __construct(int $id, $client_secret=null, $server_secret=null, string $endpoint="https://api-gatey.florgon.com/v1"){
        $this->id = $id;
        if(is_null($client_secret) and is_null($server_secret)){
            throw new GateyAuthException("Please specify client_secret OR server_secret.",100);
        }
        $this->client_secret = $client_secret;
        $this->server_secret = $server_secret;
        $this->endpoint=$endpoint;
    }

    private function custom_file_get_contents(string $url) {
        return json_decode(file_get_contents(
            $url,
            false,
            stream_context_create(
                array(
                    'http' => array(
                        'ignore_errors' => true
                    )
                )
            )
        ),true);
    }

    public function capture_message(string $message, string $level="DEBUG", $exception=null):int{
        $exception_json="";
        if(!is_null($exception)){
            $exception_f=$exception->getMessage()."\nin file: ".$exception->getFile()."\nat line: ".$exception->getLine()."\nError code: ".$exception->getCode()."\n\nTraceback: ".$exception->getTraceAsString();
            $exception_j=json_encode(['description'=>$exception_f,'class'=>get_class($exception), 'traceback'=> array(), 'variables'=> ['globals'=> new stdClass(), 'locals'=> new stdClass()]]);
            $exception_json="&exception=".urlencode($exception_j);
        }
        $tags=urlencode(json_encode(["sdk"=> "PHP Gatey SDK", "platform"=> $_SERVER['SERVER_SOFTWARE']]));
        
        if(is_null($this->client_secret)){
            $secret = '&server_secret='.urlencode($this->server_secret);
        }else{
            $secret = '&client_secret='.urlencode($this->client_secret);
        }
        $result=$this->custom_file_get_contents($this->endpoint.'/event.capture?project_id='.urlencode(strval($this->id)).$secret.'&level='.urlencode($level).'&message='.urlencode($message).'&tags='.$tags.'&captured_at='.urlencode(strval(microtime(true))).$exception_json);
        if(array_key_exists('success',$result) and !is_null($result['success'])){
            return $result['success']['event']['id'];
        }
        throw new GateyAPIException("API error while capturing message: ".$result['error']['message'], intval($result['error']['code']));
        return -1;
    }

    public function catch(Throwable $exception){
        $this->capture_message($exception->getMessage(), "ERROR", $exception);
    }
}