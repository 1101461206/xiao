<?php

namespace app\api\model;
use think\Model;
use app\api\model\HttpModel as http;
use app\api\model\SignatureModel as sig;
use app\api\model\FaceModel as face;
use app\api\model\Tx_coqModel as txcomq;





class ApiModel extends Model
{
    protected $ssecretId="AKIDLyJtIVYHpVxstfmfsb8JfgZLunQGjPgn";
    protected $secretKey="2ZpuBJGKX0JrHcR21mVlW3xlRSI4ezHL";
    protected $region="ap-chengdu";


    public function https($url)
    {
        $http=new http();
        $info=$http->https($url);
        return $info;
    }

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

    public function action($msg){
        $info=json_decode($msg['msgBody'],true);
        $model=$info['model'];
        switch ($model){
            case "form":
                $txcomq=new txcomq();
                $info=$txcomq->tx_form($info);
                return $info;
                break;
            default:
                break;
        }

    }


}