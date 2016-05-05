<?php 


class BaseModel extends Model {

    const TBL_PROVINCE = 'province';
    const TBL_USER = 'user';
    const TBL_USER_PROFILE = 'user_profile';

    public function getUser($where='id', $val)
    {
        $this->_db->join(SELF::TBL_USER_PROFILE.' up', 'up.userId=u.id', 'LEFT');
        $this->_db->join(SELF::TBL_PROVINCE.' pro', 'up.provinceId=pro.id', 'LEFT');
        $this->_db->where($where, $val);
        $data = $this->_db->getOne(SELF::TBL_USER.' u', 'u.mobile, u.salt, u.password, up.nickName, up.gender, up.provinceId, up.userId, pro.provinceName');
        if(!$data) {
            return FALSE;
        } 
        return $data;

    }

    public function getProvince($id=FALSE)
    {
        if ($id) {
            $this->_db->where('id', $id);
            $data = $this->_db->getOne(SELF::TBL_PROVINCE);
        }
        else
        {
            $data = $this->_db->get(SELF::TBL_PROVINCE, null, 'id, provinceName');
        }
        if(!$data) {
            return FALSE;
        } 
        return $data;
        

    }

    public function signUp($user, $profile)
    {
        $this->_db->where('mobile', $user['mobile']);
        if ($this->_db->getOne(SELF::TBL_USER)){
            $this->Response->error('40030', $user['mobile']);
        }
        $this->_db->startTransaction();
        
        if ( ! $userId = $this->_db->insert(self::TBL_USER, $user) ) {
             error_log('insert user data error '. $this->_db->getLastError());
             $this->_db->rollback();
             return FALSE;
        }

        $profile['userId'] = $userId;

        if ( ! $this->_db->insert(self::TBL_USER_PROFILE, $profile) ) {
             error_log('insert user profile data error '. $this->_db->getLastError());
             $this->_db->rollback();
             return FALSE;
        }
        $this->_db->commit();
        return $userId;
    }

}
