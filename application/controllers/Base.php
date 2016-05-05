<?php



class BaseController extends Core
{
    /**
     *
     * 注册
     *
     *
     */    
    public function sign_upAction() 
    {
        
        // 必要参数是否存在且不为空        
        $password = ( $this->_post['password'] ?? $this->Response->error('40016')) ? : $this->Response->error('40019');
        $mobile = ( $this->_post['mobile'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40020');
        $token = ( $this->_post['csToken'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40021');
        $gender = ( $this->_post['gender'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40022');
        $provinceId = ( $this->_post['provinceId'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40023');
        $nickName = ( $this->_post['nickName'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40024');

        // 参数格式是否合法 
        $mobile = $this->Common->checkMobile($mobile) ? : $this->Response->error('40018'); 
        $password = $this->Common->checkPassword($password) ? : $this->Response->error('40025'); 
        if ($gender != 'male' && $gender != 'female') $this->Response->error('40026');
            
        // 现居地
        $provinceData = $this->BaseModel->getProvince($provinceId) ? : $this->Response->error('40027'); 
            
        // 昵称
        $nickName = $this->Common->checkNickName($nickName) ? : $this->Response->error('40028');
        // 获取缓存中的手机验证码,如果没有则认为验证码过期或没有调用验证码api
        $cachedMobile = $this->Cache->get('mobile', $mobile) ? : $this->Response->error('40105');
        $cachedToken = $cachedMobile->csToken; 
        // 注册token比对
        if ($cachedToken != $token) $this->Response->error('40017');
        // 注册
        $salt = $this->Common->random_string('alnum', 8);
        $user = [
                'password'=>md5($password.$salt),
                'salt'=>$salt,
                'mobile'=>$mobile,
        ];
        $profile = [
                'nickName'=>$nickName,
                'gender'=>$gender,
                'provinceId'=>$provinceId,
        ];
        // 插入数据,成功缓存session,返回session
        if ($userId = $this->BaseModel->signUp($user, $profile) ){
            $sessionToken = $this->Common->random_string('alnum', '60'); 
            $redisVal = [
                'userId'=>$userId,
                'sessionToken'=>$sessionToken,
            ];
            $this->Cache->set('user', $userId, $redisVal, 7200);
            $this->Response->success([
                                        'sessionToken'=>$sessionToken,
                                        'userInfo' => [
                                                         'userId'       => $userId,
                                                         'nickName'     => $nickName,
                                                         'provinceId'   => $provinceId,
                                                         'provinceName' => $provinceData['provinceName'],
                                                         'gender'       => $gender,
                                                         'mobile'       => $mobile,
                                        ]

            ]);
        }
        else 
        {
                $this->Response->error('40029', $user);
        } 
    }

    /**
     *
     * 登陆
     *
     *
     */
    public function sign_inAction()
    {
        // 必要参数是否存在且不为空        
        $password = ( $this->_post['password'] ?? $this->Response->error('40016')) ? : $this->Response->error('40019');
        $mobile = ( $this->_post['mobile'] ?? $this->Response->error('40016') ) ? : $this->Response->error('40020');
        // 获取用户信息, 无此用户,报错
        $user = $this->BaseModel->getUser('mobile', $mobile) ? : $this->Response->error('40031', $mobile);
        // 数据库用户信息
        $salt = $user['salt'] ?? '';
        $passwordMd5 = $user['password'] ?? '';
        $provinceId = $user['provinceId'] ?? '';
        $provinceName = $user['provinceName'] ?? '';
        $gender = $user['gender'] ?? '';
        $userId = $user['userId'] ?? '';
        $nickName  = $user['nickName'] ?? '';
        
        // 密码比对成功,根据缓存查找sessionToken
        if ( md5($password.$salt) == $passwordMd5 )
        {
            // 如果缓存没有到期,返回缓存中得sessionToken
            if ($sessionUser = $this->Cache->get('user', $userId)) {
                $sessionToken = $sessionUser->sessionToken;
            }
            else 
            {
                // 缓存到期,重新生成新sessionToken
                $sessionToken = $this->Common->random_string('alnum', '60'); 
                $redisVal = [
                    'userId'=>$userId,
                    'sessionToken'=>$sessionToken, 
                ];
                $this->Cache->set('user', $userId, $redisVal, 7200);

            }
            $this->Response->success([
                                        'sessionToken'=>$sessionToken,
                                        'userInfo' => [
                                                         'userId'       => $userId,
                                                         'nickName'     => $nickName,
                                                         'provinceId'   => $provinceId,
                                                         'provinceName' => $provinceName,
                                                         'gender'       => $gender,
                                                         'mobile'       => $mobile,
                                        ]

            ]);

        }
        $this->Response->error('40032', $mobile);
    }

    public function send_smsAction()
    {
        $appName = $this->_post['appName'] ?? $this->Response->error('40101');
        $mobile = $this->_post['mobile'] ?? $this->Response->error('40101');
        $sendSms = [new Tao($appName), 'sendSms'];
        $sendSms($mobile);
    }

    public function validate_smsAction()
    {
        $mobile = ($this->_post['mobile'] ?? '') ? : $this->Response->error('40106');
        $code = ($this->_post['code'] ?? '') ? : $this->Response->error('40106');
        $cachedMobile = $this->Cache->get('mobile', $mobile) ? : $this->Response->error('40105');
        $cachedCode = $cachedMobile->code; 
        $cachedToken = $cachedMobile->csToken; 
        if ($cachedCode == $code)
        {
            $this->Response->success(['csToken'=>$cachedToken]);
        }
        $this->Response->error('40107');

    }

    public function get_provinceAction()
    {
        $data = $this->BaseModel->getProvince();
        $data = ['provinces'=>$data];
        $this->Response->success($data);
    }


}
