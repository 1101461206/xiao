<?php
namespace app\api\model;

use think\Model;
use app\api\model\SignatureModel as sig;



class PersonnelModel extends ApiModel {

    function __construct()
    {

        parent::__construct();
    }
/**
 * 获取人员库列表
 */
    public function pool(){
//        $sig=new sig();
        $rand_num=$this->signature('random',array('num'=>4));
        $time=time();
        $data=array(
            'Action'=>'GetGroupList',
            'Version'=>'2018-03-01',
            'Region'=>"",
            'Offset'=>'0',
            'Limit'=>10,
            'Timestamp'=>$time,
            'Nonce'=>$rand_num,
            'SecretId'=>$this->ssecretId,
        );
        $s_url = "GETiai.ap-chengdu.tencentcloudapi.com/?";
        $signature=$this->signature("tx_make",array('data'=>$data,'key'=>$this->secretKey,'url'=>$s_url));
        $url="https://iai.ap-chengdu.tencentcloudapi.com/?".$signature['par']."&Signature=".$signature['signStr'];
        $info=$this->https($url);
        return $info;



    }

    /**
     * 创建人员
     * @GroupId     待加入的人员库ID。
     * @PersonName  人员名称。[1，60]个字符，可修改，可重复。
     * @PersonId    人员ID
     * https://cloud.tencent.com/document/product/867/32793
     */
    public function CreatePerson($data){
        $time=time();
        $number=$this->signature('random',array('num'=>4));
        $data_a=array(
            'Action'=>"CreatePerson",
            'Version'=>"2018-03-01",
            'Region'=>"",
            'GroupId'=>$data['GroupId'],
            'PersonName'=>$data['PersonName'],
            'PersonId'=>$data['PersonId'],
            'Url'=>$data['img'],
            'Timestamp'=>$time,
            'Nonce'=>$number,
            'SecretId'=>$this->ssecretId,
        );
        $s_url="GETiai.ap-chengdu.tencentcloudapi.com/?";
        $sig=$this->signature('tx_make',array('data'=>$data_a,'key'=>$this->secretKey,'url'=>$s_url));
        $url="https://iai.ap-chengdu.tencentcloudapi.com/?".$sig['par']."&Signature=".$sig['signStr'];
        $info=$this->https($url);
        return $info;

    }

    /**
     * 上传到人员库,增加人脸
     */
    public function CreateFace($data){
        $time=time();
        $number=$this->signature('random',array('num'=>4));
        $data_a=array(
            'Action'=>"CreateFace",
            'Version'=>"2018-03-01",
            'Urls.0'=>$data['img'],
            'Region'=>'',
            'Timestamp'=>$time,
            'Nonce'=>$number,
            'SecretId'=>$this->ssecretId,
            'PersonId'=>"sds",
        );
        $url = "GETiai.ap-chengdu.tencentcloudapi.com/?";

        $signStr=$this->signature('tx_make',array('data'=>$data_a,'key'=>$this->secretKey,'url'=>$url));
        $url1 = "https://iai.ap-chengdu.tencentcloudapi.com/?" . $signStr['par'] . "&Signature=" . $signStr['signStr'];
        $info=$this->https($url1);
        var_dump($info);

    }

}