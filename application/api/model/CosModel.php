<?php
namespace app\api\model;

use think\Db;
use think\File;
use think\Model;
use think\Request;
require ROOT_PATH.'/extend/cos-php-sdk-v5/vendor/autoload.php';
use app\api\model\FaceModel as face;
class CosModel extends Model{
    protected $table = "";
    protected $Path=ROOT_PATH;
    protected $ssecretId="AKIDLyJtIVYHpVxstfmfsb8JfgZLunQGjPgn";
    protected $secretKey="2ZpuBJGKX0JrHcR21mVlW3xlRSI4ezHL";
    protected $region="ap-chengdu";

    public function cos($key){

        $cosClient = new \Qcloud\Cos\Client(array(
            'region' => $this->region, #地域，如ap-guangzhou,ap-beijing-1
            'credentials' => array(
                'secretId' => $this->ssecretId,
                'secretKey' => $this->secretKey,
            ),
        ));

        // 若初始化 Client 时未填写 appId，则 bucket 的命名规则为{name}-{appid} ，此处填写的存储桶名称必须为此格式
        $bucket = 'xiao-1256168726';
        $key = $key;
        $local_path = $this->Path."/public/uploads/".$key;

        try {
            $result = $cosClient->putObject(array(
                'Bucket' => $bucket,
                'Key' => $key,
                'Body' => file_get_contents($local_path)
            ));
           // print_r($result);
            # 可以直接通过$result读出返回结果
            $img_url=$result['ObjectURL'];
            $info=array();
            if(empty($img_url)){
                $info['code']=0;
                $info['error']=array(
                    'mag'=>"上传失败",
                );
            }else{
                $info['code']=1;
                $info['mag']=array(
                    'img_url'=>$img_url,
                );
            }
            return $info;

        } catch (\Exception $e) {
            echo($e);
        }

    }

}