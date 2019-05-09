<?php
namespace app\api\model;

use think\Db;
use think\File;
use think\Model;
use think\Request;
use app\api\model\CosModel as cos;

class FileModel extends Model{

    protected $table = "";
    protected $uploadPath=ROOT_PATH.'/public/uploads/';

    public function file(){

        $file = request()->file('file');
        if ($file) {
            $info = $file->move($this->uploadPath);
            if ($info) {
                $file = $info->getSaveName();
               $info=array(
                   'code'=>1,
                   'mag'=>array(
                       'img'=>$file,
                   ),
               );
            }
        }else{
            $info=array(
                'code'=>0,
                'error'=>array(
                    'mag'=>"上传失败",
                ),

            );
        }
        return $info;
    }

}