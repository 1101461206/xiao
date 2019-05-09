<?php
namespace app\api\model;

use think\Model;


class SignatureModel extends Model{
    /**
     * https://cloud.tencent.com/document/api/867/32775
     */

    public function tx_make($data,$key,$url){
        ksort($data);
        $uu = array();
        foreach ($data as $k => $v) {
            $uu[] = $k . "=" . $v;
        }
        $par = implode("&", $uu);
        $url .= $par;
        $signStr = base64_encode(hash_hmac('sha1', $url,$key, true));
        //$signStr=urlencode($signStr);
        $info=array(
            'par'=>$par,
            'signStr'=>$signStr,
        );
        return $info;
    }

    public function random($num){
        $a=array('1','2','3','4','5','6','7','8','9','0');
        $number='';
        for($i=0;$i<$num;$i++){
            $number.=array_rand($a,1);
        }
        return $number;
    }

}