<?php

class Yunpian {

    private $_apiKey = FALSE;
    private $_requestSmsCode_url = 'https://sms.yunpian.com/v1/sms/send.json';

    public function __construct($appName)
    {
        $this->xError = Yaf_Registry::get("xError");
        $this->Response = Yaf_Registry::get("Response");
        $this->Curl = Yaf_Registry::get("Curl");
        $YunpianModel = new YunpianModel();
        if (!$YunData = $YunpianModel->getAppData($appName)) {
            error_log('appName not exits! appName='.$appName); 
            $this->Response->error($this->xError['APP_NAME_NOT_EXITS']);
        }
        $this->_apiKey = trim($YunData['apiKey']);
    }

    public function sendSms($mobile)
    {
        $data = array(
            'mobile'=> $mobile,
            'apikey'=> $this->_apiKey,
            'text'  => '【爬梯APP】您的验证码是2456。如非本人操作，请忽略本短信',
        );
#echo '<pre>';print_r($data);echo '</pre>';exit(); 
        $response = $this->Curl->get_response($this->_requestSmsCode_url, 
        'POST',
        $data
        );
echo '<pre>';print_r($response);echo '</pre>';exit(); 
    }
}
