<?php
namespace app\api\model;

use think\Exception;
use think\Model;
use think\helper;
use app\api\model\SignatureModel as sig;
use think\facade\Log;



class CmqModel extends ApiModel {

    function __construct()
    {

        parent::__construct();
    }

    function sendcmq($name,$msg){
        $time=time();
        $num=$this->signature('random',array('num'=>4));
        $msg['type']="tx";
        $msg=json_encode($msg);
        $data['data']=array(
            'queueName'=>$name,
            'msgBody'=>$msg,
            'Action'=>"SendMessage",
            'Region'=>"cd",
            'Timestamp'=>$time,
            'Nonce'=>$num,
            'SecretId'=>$this->ssecretId,
        );
        $data['url']="GETcmq-queue-cd.api.tencentyun.com/v2/index.php?";
        $data['key']=$this->secretKey;
        $Signature=$this->signature("tx_make",$data);
        $url="http://cmq-queue-cd.api.tencentyun.com/v2/index.php?".$Signature['par']."&Signature=".$Signature['signStr'];
        $info=$this->https($url);
        $info=json_decode($info,true);
        $info['msg']=$msg;
        //错误机制
        if($info['code']>0){
           // var_dump($info);
          // Log::write($info,'info');
            trace("dfd",'error');
            return 0;
        }else{
            return 1;
        }
    }


    function receive($name,$msg){
        echo date("Y-m-d H:i:s")."<br>";
        $sleep = 1000000 * rand(1, 3);
        $i=1;
        while ($i<2){
            usleep($sleep);//延迟执行
            echo date("Y-m-d H:i:s");
            $time=time();
            $num=$this->signature('random',array('num'=>4));
            $data['data']=array(
                'queueName'=>$name,
                'Action'=>"ReceiveMessage",
                'Region'=>"cd",
                'Timestamp'=>$time,
                'Nonce'=>$num,
                'SecretId'=>$this->ssecretId,
            );
            $data['url']="GETcmq-queue-cd.api.tencentyun.com/v2/index.php?";
            $data['key']=$this->secretKey;
            $Signature=$this->signature("tx_make",$data);
            $url="http://cmq-queue-cd.api.tencentyun.com/v2/index.php?".$Signature['par']."&Signature=".$Signature['signStr'];
            $info=$this->https($url);
            $info=json_decode($info,true);
            echo "<pre>";
            var_dump($info);
            echo "<pre>";
            if($info['code']>0){
                $sleep=2000000;
            }else{
                $sleep=20000;
            }

            trace($info,'info');
           //处理消息
            try{
                if(!empty($info['msgBody'])){
                    $this->action($info);

                }

            }catch (\Exception $e){

            }


            //$this->del('ceshi',$info['receiptHandle']);
            $i++;

        }

    }

    function del($name,$id){
        $time=time();
        $num=$this->signature('random',array('num'=>4));
        $data['data']=array(
            'queueName'=>$name,
            'Action'=>"DeleteMessage",
            'Region'=>"cd",
            'Timestamp'=>$time,
            'Nonce'=>$num,
            'SecretId'=>$this->ssecretId,
            'receiptHandle'=>$id,
        );
        $data['url']="GETcmq-queue-cd.api.tencentyun.com/v2/index.php?";
        $data['key']=$this->secretKey;
        $Signature=$this->signature("tx_make",$data);
        $url="http://cmq-queue-cd.api.tencentyun.com/v2/index.php?".$Signature['par']."&Signature=".$Signature['signStr'];
        $info=$this->https($url);
        echo "<br>";
        echo "<pre>";
        var_dump($info);
        echo "<pre>";


    }



}