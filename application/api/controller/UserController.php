<?php

namespace app\api\controller;

use app\api\model\FileModel as file;
use think\App;
use think\Controller;
use think\Request;
use think\facade\Config;
use think\facade\Log;
use think\helper;
use think\Db;
use app\api\model\CosModel as cos;
use app\api\model\FaceModel as face;
use app\api\model\HttpModel as http;
use app\api\model\PersonnelModel as per;


class UserController extends ApiController
{

    /**
     * grant_type 授权类型，此处只需填写 authorization_code
     * js_code    登录时获取的 code
     */
    public function indexAction()
    {
        $param = request()->post();

        switch ($param['type']){
            case "login":
                $url="https://api.weixin.qq.com/sns/jscode2session?appid=".config('wx_appid')."&secret=".config('wx_appsecret')."&js_code=".$param['code']."&grant_type=authorization_code";
                $http=new http();
                $info=$http->https($url);
                echo $info;
                break;
            default:
                echo "错误";
                break;
        }

    }


    /**
     * 查询人员 填写是否中断过
     */
    public function checknameAction(){
        $param=request()->post("openid");
       $check=Db::table('xiao_form')->where('openid',$param)->find();
       if(empty($check)){
           echo json_encode(array('num'=>0));
       }else{
           echo json_encode($check);
       }



    }

    /**
     * 添加人员
     */
    public function userAction(){
        $param = request()->post();
        switch($param['num']){
            case 1:
                $data=array(
                    'openid'=>$param['openid'],
                    'num'=>$param['num'],
                );
                $data['longitude']=$param['longitude'];
                $data['latitude']=$param['latitude'];
                $check=Db::table('xiao_form')->where('openid',$param['openid'])->find();
                if($check){
                    echo json_encode(0);
                }else{
                    $in = Db::name('xiao_form')->insertGetId($data);
                    if($in){
                        echo 1;
                    }
                }
                break;
            case 2:
                echo 3;
             //  $data=['name'=>$param['name'],'mobile'=>(int)$param['mobile'],'num'=>$param['num']];
               $up=Db::table('xiao_form')
                  ->where('openid',$param['openid'])
                   ->data(['num'=>$param['num'],'name'=>$param['name'],'mobile'=>(int)$param['mobile']])
                  ->update();
               if($up>0){
                   echo 1;
               }else{
                   echo 0;
            }
                break;
            default:
                echo 0;
        }






    }


    public function ceshiAction(){

    trace("dfdf",'error');

//        Log::write('12312x','error');
        $msg=array(
            'model'=>'face',
            'action'=>'',
            'msg'=>array(
                'openid'=>111,
                "numam"=>"erer",
            ),
        );
  //     $msg=json_encode($msg);
//        //$this->Cmq('sendcmq',"ceshi",$msg);
      $this->Cmq("receive","ceshi",$msg);

    }

    /**
     * 用户上传图片
     */
    public function imgAction(){
           $openid=request()->post('openid');
           $submit=request()->post('submit');
           //上传到本地
           $file=new file();
           $local_info=$file->file();
           if(empty($local_info['error'])){
               $local_img=$local_info['mag']['img'];
               if(!empty($local_img)){
                   $check=Db::table('xiao_form')->where('openid',$openid)->find();
                   $check_img=Db::table('xiao_form_img')->where('f_id',$check['id'])->find();
                   trace($check_img,'mysql');
                   if($check_img){
                       $in=Db::name('xiao_form_img')
                           ->where('id',$check_img['id'])
                           ->data(['img'=>$local_img,'img_oos'=>""])
                           ->update();
                   }else{
                       $data=['f_id'=>$check['id'],'img'=>$local_img];
                       $in=Db::name('xiao_form_img')->insert($data);
                   }
                   if($in){
                       if($submit){
                           $msg=array(
                                'model'=>'form',
                                'msg'=>array(
                                    'openid'=>$openid,
                                ),
                            );
                           $send_info=$this->Cmq('sendcmq',"ceshi",$msg);
                           echo $send_info;
                       }
                   }
               }
           }else{
               trace($local_info['error']['mag'],'error');

           }

    }




//$cos_img=$this->CosImg($local_img);
//if(empty($cos_img['error'])){
//$cos_img_url=$cos_img['mag']['img_url'];

//if($in){
//
//
//}
////$info=$this->DetectFace($cos_img_url,1);
////  echo $info;
//}else{
//    echo $cos_img['error']['mag'];
//}
//


    /**
     * 上传到oss
     */
    public function CosImg($local_img){
        $local_cos=new cos();
        $cos_img=$local_cos->cos($local_img);
        return $cos_img;

    }
    /**
     * 人脸分析
     */
    public function DetectFace($cos_img,$type,$num){
        //$face=new face();
        $detectface=$this->face("DetectFace",$cos_img,$type,$num);
        return $detectface;
    }

    /**
     * 获取人员库列表
     */
    public function perAction(){
        $info=$this->per('pool');
        return $info;

    }

    /**
     * 创建人员
     */
    public function CreatePerson($data){
        $info=$this->per('CreatePerson',$data);
        return $info;

    }

    /**
     * 增加人员到人员库
     */
    public function CreateFace($data){
        $info=$this->per('CreateFace',$data);
        return $info;

    }

    /**
     * 人脸搜索
     */
    public function SearchFaces($img_url,$type){
        $info=$this->face("SearchFaces",$img_url,$type);
        return $info;
    }

    /**
     * 发送消息队列
     */

    public function Cmq($action,$name,$msg){
        $info=$this->tx_cmq($action,$name,$msg);
        return $info;

    }

    public function tx_DetectFace($msg){


    }
}
