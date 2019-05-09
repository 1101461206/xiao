<?php

namespace app\api\model;
use think\Model;
use app\api\model\HttpModel as http;
use app\api\model\SignatureModel as sig;



class ImgModel extends Model
{
    protected $ssecretId="AKIDLyJtIVYHpVxstfmfsb8JfgZLunQGjPgn";
    protected $secretKey="2ZpuBJGKX0JrHcR21mVlW3xlRSI4ezHL";
    protected $region="ap-chengdu";


    public function signature($action,$data){
        $sig=new sig();
        switch($action){
            case "random":
                $info=$sig->random($data['num']);
                return $info;
                break;
            case "tx_make":
                $info=$sig->tx_make($data['data'],$data['key'],$data['url']);
                return $info;
                break;
        }
    }

}