<?php 


class AppModel extends Model {

    const TBL_APP_DEVELOPER = 'app_developer';

    public function getAppDeveloper($key, $val)
    {
        $this->_db->where($key, $val);
        $data = $this->_db->getOne(SELF::TBL_APP_DEVELOPER);
        if(!$data) {
            return FALSE;
        } 
        return $data;
        

    }

}
