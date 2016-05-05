<?php

class Curl
{
    public function get_response($url,$action='GET',$data='')
    {
        $init = curl_init($url);
        # curl_setopt($init, CURLOPT_URL,$url);
        curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
        if($action == 'POST')
        {
            curl_setopt($init, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($init, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json',
                'Accept:application/json;charset=utf-8;',
                'Content-Type:application/x-www-form-urlencoded;charset=utf-8;')
//                'Content-Length: ' . strlen($data))
            );
            curl_setopt($init, CURLOPT_POSTFIELDS, http_build_query($data));
            # curl_setopt($init, CURLOPT_POST, 1);
        }
        curl_setopt($init, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($init, CURLOPT_BINARYTRANSFER, 1);
        curl_setopt($init, CURLOPT_MAXREDIRS, 1);
        #curl_setopt($init, CURLOPT_SSLVERSION, 1);
        #curl_setopt($init, CURLOPT_SSL_VERIFYPEER, FALSE); 
        #curl_setopt($init, CURLOPT_SSL_VERIFYHOST, FALSE);  
        $html = curl_exec($init);
        $info = curl_getinfo($init);
        curl_close($init);

        # return array('html' => $html, 'info' => $info);
        return json_decode($html);
    }

}
