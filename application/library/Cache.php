<?php
declare(strict_types=1);

require '../vendor/autoload.php';
use Predis\Client as Predis;

class Cache {


    public function __construct()
    {
        Yaf_Loader::import(APP_PATH. "/application/conf/redis.php");
        global $redis_prefix;
        global $redis_config;
        $this->RP = $redis_prefix;
        $this->Predis = new Predis($redis_config);
        $this->Response = new Response(); 
        $this->Common = new Common();
    }

    public function set(string $redisPrefix, string $redisId, array $redisValue, int $expire)
    {
        if ( ! $this->Predis
                    ->transaction()
                    ->set($this->RP[$redisPrefix].md5($redisId), json_encode($redisValue), 'ex', $expire)
                    ->get($this->RP[$redisPrefix].md5($redisId))
                    ->execute() )
        {
                    $this->Response->error('40202', $user['id']);
        } 
    }
    
    public function get(string $redisPrefix, string $redisId)
    {
        if ($cacheVal = $this->Predis->get($this->RP[$redisPrefix].md5($redisId)) ) {
            return json_decode($cacheVal);    
        }
        else
        {
            return FALSE; 
        } 

    }
}
