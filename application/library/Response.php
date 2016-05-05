<?php

# namespace Fit;

header('Content-Type: application/json');
#header('Accept:application/json;charset=utf-8;');
#header('Content-Type:application/x-www-form-urlencoded;charset=utf-8;');

class Response {

    protected $error = [

                    '40009' => '请求中缺少sessionToken!',
                    '40010' => '请求中缺少appid!',
                    '40011' => '请求中缺少Endpoint Agent!',
                    '40012' => '请求中缺少App Version!',
                    '40013' => '请求中缺少Sign!',
                    '40014' => 'app name不存在!',
                    '40015' => 'signature 比对错误!',
                    //sign up
                    '40016' => '提交的参数不全',
                    '40017' => 'csToken比对错误',
                    '40018' => '手机号不合法!!',
                    '40019' => '密码不能为空',
                    '40020' => '手机号不能为空',
                    '40021' => '注册token不能为空',
                    '40022' => '性别不能为空',
                    '40023' => '现居地不能为空',
                    '40024' => '昵称不能为空',
                    '40025' => '密码必须是6-16位数字,字母,下划线,破折号组成!',
                    '40026' => '性别不合法',
                    '40027' => '现居地不合法!!',
                    '40028' => '昵称必须是1-15位数字,字母,下划线,破折号组成!',
                    '40029' => '注册失败,请联系客服',
                    '40030' => '该手机号已被注册,请更换手机号',
                    // sign in
                    '40031' => '用户不存在!',
                    '40032' => '登陆密码错误',
                    // logout
                    '40033' => 'userId不能为空',
                    
                    // sms
                    '40101' => 'appName 或 mobile 参数不全[send sms]!',
                    '40102' => '短信服务商返回数据格式错误!',
                    '40103' => '短信返回异常状态码,请排查日志!',
                    '40104' => '该手机号短信发送频率过快!',
                    '40105' => '该手机号短信发送时间过长,缓存已过期!',
                    '40106' => '接口参数不全[validate sms]',
                    '40107' => '验证码比对错误[validate sms]',
                    //cache
                    '40201' => 'redis store mobile data error!',
                    '40202' => 'redis store session token data error!',
                ];

    public function error($errorCode, $debug=FALSE)
    {
        $errorMSG = $this->error[$errorCode] ?? FALSE;
        if ( ! $errorCode || ! $errorMSG) {
            error_log('errcode and errmsg miss, errorCode='.$errorCode .'[Lib Response.php]');
            exit();
        }
        $error = [
            'errcode'=>$errorCode,
            'errmsg'=>$errorMSG,
        ];
        error_log('Response error '.json_encode($error));
        if ($debug) {
            error_log('debug data '.json_encode($debug));
        }

        foreach($error as $key => $val){
            $error[$key] = urlencode($val);
        }
        echo urldecode(json_encode($error));
        exit();
    }

    public function success($data)
    {
        echo json_encode($data);
        exit();
    }

}
