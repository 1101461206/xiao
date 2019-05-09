<?php
namespace app\api\model;

use think\Model;


class HttpModel extends Model{

    public function https($url){
        $ch = curl_init();
        //要访问的地址
        curl_setopt($ch, CURLOPT_URL, $url);
        //执行结果是否被返回，0是返回，1是不返回
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        // 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
        // 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 2);// 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);// 从证书中检查SSL加密算法是否存在
        $output = curl_exec($ch);//执行并获取数据
        curl_close($ch);
        //$info = json_decode($output, true);
        return $output;

    }


}