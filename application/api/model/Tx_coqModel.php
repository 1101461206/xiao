<?php

namespace app\api\model;
use think\Model;
use app\api\model\HttpModel as http;
use app\api\model\SignatureModel as sig;
use app\api\model\FaceModel as face;
use app\api\model\CosModel as cos;
use think\Db;




class Tx_coqModel extends ApiModel
{

    function tx_form($data){
        $openid=$data['msg']['openid'];

//        $img=Db::name('xiao_form')
//            ->where('openid',$openid)
//            ->find();

       $sql="select * from xiao_form_img i,(select id from xiao_form where openid=$openid) t where i.f_id=t.id";
       $img=Db::query($sql);
        var_dump($sql);
//        if($img['img_oos']){
//
//        }


    }




}