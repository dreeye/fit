<?php

class YunpianModel extends Model {

    const TBL_YUNPIAN = 'yunpian'; 

    public function getAppData($appName)
    {
        $this->_db->where('appName', $appName);
        $data = $this->_db->getOne(SELF::TBL_YUNPIAN);
        if(!$data) {
            return FALSE;
        } 
        return $data;
    }

}
