<?php

require '../vendor/autoload.php';

use Predis\Client as Predis;

class Core extends Yaf_Controller_Abstract {

    protected $_appId;
    protected $_sign;
    protected $_token;
    protected $_endPoint;
    protected $_appVersion;
    protected $_post;

    protected $Response;

    protected function init()
    {
        $this->Common = new Common();
        $this->Response = new Response(); 
        $this->BaseModel = new BaseModel();
        $this->Cache = new Cache();
        $this->_appId = ($_SERVER['HTTP_X_LECLY_ID'] ?? '') ? : $this->Response->error('40010');
        $this->_sign = ($_SERVER['HTTP_X_LECLY_SIGN'] ?? '') ? : $this->Response->error('40013');
        $this->_endPoint = ($_SERVER['HTTP_X_LECLY_ENDPOINT_AGENT'] ?? '') ? : $this->Response->error('40011');
        $this->_appVersion = ($_SERVER['HTTP_X_LECLY_APP_VERSION'] ?? '') ? : $this->Response->error('40012');
        // 验证请求合法性
        $this->_validate_sign();
        $this->_sessionToken = $_SERVER['HTTP_X_LECLY_SESSION_TOKEN'] ?? '';
        $this->_post = json_decode(file_get_contents('php://input'), TRUE); 
    }


    private function _validate_sign()
    {
        $signArray = explode(',', $this->_sign);
        $signature = $signArray[0] ?? FALSE;
        $timestamp = $signArray[1] ?? FALSE;
        if ($signature && $timestamp) {
            $AppModel = new AppModel();
            $appDevelopData = $AppModel->getAppDeveloper('app_id', $this->_appId); 
            $appKey = $appDevelopData['app_key'];
            // signature 比对失败
            if ($signature != md5($timestamp.$appKey)) {
                $this->Response->error('40015', ['timestamp'=>$timestamp, 'appKey'=>$appKey]);
                exit();
            }
        }
    }

}
