<?php

class Tao {

    private $_appKey = FALSE;
    private $_appSecret = FALSE;
    private $_appSmsName = FALSE;

    public function __construct($appName)
    {
        $this->Cache = new Cache();
        $this->Response = new Response(); 
        $this->Common = new Common();
        $TaoModel = new TaoModel();
        $TaoData = $TaoModel->getAppData($appName) ? : $this->Response->error('40014', $appName);
        $this->_appKey = trim($TaoData['appKey']);
        $this->_appSecret = trim($TaoData['appSecret']);
        $this->_appSmsName = trim($TaoData['smsName']);
    }

    public function sendSms($mobile)
    {
        // 检查发送验证码频率
        if ($cacheData = $this->Cache->get('mobile', $mobile)) $this->_checkCache($cacheData);
        $code = $this->Common->random();
        $data = [
            'createTime' => time(),
            'code' => $code,
            'csToken' => $this->Common->random_string('alnum', 60)
        ];
        $this->Cache->set('mobile', $mobile, $data, 3600);
        // 发送验证码
        $this->_msm($mobile, $code);
        
    }

    private function _msm($mobile, $code)
    {
        Yaf_Loader::import(APP_PATH. "/application/library/Tao/TopSdk.php");
        $c = new Tao_top_TopClient;
        $c->appkey = $this->_appKey;
        $c->secretKey = $this->_appSecret;
        $c->format = 'json';
        $req = new Tao_top_request_AlibabaAliqinFcSmsNumSendRequest;
        $req->setSmsType("normal");
        $req->setSmsFreeSignName($this->_appSmsName);
        // 短信模板的参数,验证码和产品名
        $SmsParam = json_encode([
            'code' => (string)$code,
            'product' => $this->_appSmsName.'APP',
        ]); 
        $req->setSmsParam($SmsParam);
        $req->setRecNum($mobile);
        // 短信模板
        $req->setSmsTemplateCode("SMS_3595216");
        $resp = $c->execute($req);
        $errCode = $resp->result->err_code ?? FALSE;
        if ($errCode === FALSE) {
            $this->Response->error(40102, $resp);
        } 
        elseif ($errCode == 0)
        {
            $this->Response->success(['status'=>'ok']);
        }
        else
        {
            $this->Response->error('40103', $resp);

        }

    }

    /**
     * 如果缓存内的时间没有超过60秒,说明短信频率过快
     *
     *
     *
     */
    private function _checkCache($cacheData)
    {
        $createTime = $cacheData->createTime;
        if ($createTime + 60 > time() ) {
            $this->Response->error('40104', $cacheData);
        }
    }
}
