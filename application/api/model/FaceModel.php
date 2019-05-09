<?php
namespace app\api\model;

use think\Db;
use think\File;
use think\Model;
use think\Request;
use app\api\model\SignatureModel as signature;
use app\api\model\HttpModel as http;

class FaceModel extends Model{

    protected $table = "tx_img";
    protected $Path=ROOT_PATH;
    protected $ssecretId="AKIDLyJtIVYHpVxstfmfsb8JfgZLunQGjPgn";
    protected $secretKey="2ZpuBJGKX0JrHcR21mVlW3xlRSI4ezHL";
    protected $region="ap-chengdu";
    /**
     * https://cloud.tencent.com/document/api/867/32800#1.-.E6.8E.A5.E5.8F.A3.E6.8F.8F.E8.BF.B0
     * https://cloud.tencent.com/document/api/867/32807
     * NeedFaceAttributes   是否需要返回人脸属性信息（FaceAttributesInfo）。0 为不需要返回，1 为需要返回。默认为 0。
     *                      提取人脸属性信息较为耗时，如不需要人脸属性信息，建议关闭此项功能，加快人脸检测速度。
     * NeedQualityDetection 是否开启质量检测。0 为关闭，1 为开启。默认为 0。
     * Image  图片 base64 数据。支持PNG、JPG、JPEG、BMP，不支持 GIF 图片。
     */
    public function DetectFace($img_url,$type,$num)
    {
        $signature = new signature();
        $time = time();
        $number = $signature->random(4);
        $su = array(
            'Action' => 'DetectFace',
            'MaxFaceNum' => $num,
            'MinFaceSize' => 50,
            'Url' => $img_url,
            'NeedFaceAttributes' => $type,
            'NeedQualityDetection' => 1,
            'Version' => '2018-03-01',
            'Timestamp' => $time,
            'Nonce' => $number,
            'SecretId' => $this->ssecretId,
            'Token' => '',
            'Region' => 'ap-chengdu',
        );

        $url = "GETiai.ap-chengdu.tencentcloudapi.com/?";
        $signStr = $signature->tx_make($su, $this->secretKey, $url);
        $url1 = "https://iai.ap-chengdu.tencentcloudapi.com/?" . $signStr['par'] . "&Signature=" . $signStr['signStr'];
        //  exit;
        $http = new http();
        $output = $http->https($url1);
        $info = json_decode($output, true);
//        echo "<pre>";
//        var_dump($info);
//        echo "<pre>";
//        exit;
        if($num==1 && $type==1){
            $mag=$this->face_info($su,$info);
        }

        //echo $info['mag']['text'];
        return $mag;
    }

    /**
     * 人脸搜索
     */

    public function SearchFaces($img_url,$id){
        $signature=new signature();
        $rand_num=$signature->random(4);
        $time=time();
        $data=array(
            'Action'=>'SearchFaces',
            'Version'=>'2018-03-01',
            'GroupIds.0'=>$id,
            'Url'=>$img_url,
            'Region'=>$this->region,
            'Timestamp'=>$time,
            'Nonce'=>$rand_num,
            'SecretId'=>$this->ssecretId,
        );
        $url = "GETiai.ap-chengdu.tencentcloudapi.com/?";
        $Signa=$signature->tx_make($data,$this->secretKey,$url);
        $url1="iai.ap-chengdu.tencentcloudapi.com/?".$Signa['par']."&Signature=".$Signa['signStr'];
        echo $url1;
        $http=new http();
        $info=$http->https($url1);
        echo $info;
        exit;

        //return $info;



    }

    /**
     * 单人人脸检测带人脸信息反馈
     */

    public function face_info($su,$info){
            $face = $info['Response']['FaceInfos'][0];
            //人脸属性信息，包含性别( gender )、年龄( age )、表情( expression )、 魅力( beauty )、眼镜( glass )、口罩（mask）、头发（hair）和姿态 (pitch，roll，yaw )。只有当 NeedFaceAttributes 设为 1 时才返回有效信息。
            $face_info = $face['FaceAttributesInfo'];
            //人脸宽度
            $width = $face['Width'];
            //人脸高度
            $height = $face['Height'];
            //性别 [0(女)~100(男)]。
            $sex = $face_info['Gender'];
            //年龄 [0~100]。
            $age = $face_info['Age'];
            //微笑[0(正常的)~50(微笑)~100(笑)]。
            $expression = $face_info['Expression'];
            //是否有眼镜 [true,false]
            $glass = $face_info['Glass'];
            //魅力[0~100]
            $beauty = $face_info['Beauty'];
            //是否有帽子[true,false]
            $hat = $face_info['Hat'];
            //是否有口罩[true,false]
            $mask = $face_info['Mask'];
            //包含头发长度（length）、有无刘海（bang）、头发颜色（color）
            $face_hair = $face_info['Hair'];
            //0：光头，1：短发，2：中发，3：长发，4：绑发
            //注意：此字段可能返回 null，表示取不到有效值。
            $lenth = $face_hair['Length'];
            //0：有刘海，1：无刘海
            $bang = $face_hair['Bang'];
            //0：黑色，1：金色，2：棕色，3：灰白色
            $color = $face_hair['Color'];
            $face_quality = $face['FaceQualityInfo'];
            //人脸质量信息，包含质量分（score）、模糊分（sharpness）、光照分（brightness）、遮挡分（completeness）。
            //参考范围：[0,40]较差，[40,60] 一般，[60,80]较好，[80,100]很好。
            //建议：人脸入库选取70以上的图片。
            $score = $face_quality['Score'];
            //考范围：[0,40]特别模糊，[40,60]模糊，[60,80]一般，[80,100]清晰。
            //建议：人脸入库选取80以上的图片。
            $sharpness = $face_quality['Sharpness'];
            //参考范围： [0,30]偏暗，[30,70]光照正常，[70,100]偏亮。
            //建议：人脸入库选取[30,70]的图片。
            $brightness = $face_quality['Brightness'];
            $text = "";
            if (empty($info['Error'])) {
                if ($su['NeedFaceAttributes'] == 1) {
                    if ($sex > 50) {
                        $text .= "性别:男\n";
                    } else {
                        $text .= "性别:女\n";
                    }
                    $text .= "年龄:" . $age . "\n";

                    if ($expression < 50) {
                        $text .= "笑容:正常\n";
                    } elseif ($expression >= 50 && $expression < 100) {
                        $text .= "笑容:微笑\n";
                    } else {
                        $text .= "笑容:笑\n";
                    }

                    if ($glass) {
                        $text .= "有戴眼镜\n";
                    } else {
                        $text .= "没有戴眼镜\n";
                    }
                    $text .= "个人魅力值:" . $beauty . "\n";
                    if ($hat) {
                        $text .= "有带帽子无法分析\n";
                    } else {
                        $text .= "发型:";
                        switch ($lenth) {
                            case 0:
                                $text .= "光头\n";
                                break;
                            case 1:
                                $text .= "短发\n";
                                break;
                            case 2:
                                $text .= "中发\n";
                                break;
                            case 3:
                                $text .= "长发\n";
                                break;
                            case 4:
                                $text .= "绑发\n";
                                break;
                            default:
                                $text .= "无法检测\n";
                                break;
                        }
                        switch ($bang) {
                            case 0:
                                $text .= "有刘海\n";
                                break;
                            case 1:
                                $text .= "无刘海\n";
                        }
                    }
                }

                if ($score > 70 && $sharpness > 70 && ($brightness < 70 || $brightness > 30)) {
                    $info = array(
                        'code' => 1,
                        'NeedFaceAttributes' => $su['NeedFaceAttributes'],
                        'mag' => array(
                            'text' => $text,
                        ),
                    );

                } else {
                    $text .= "上传照片质量不佳，请选择清晰的照片上传";
                    $info = array(
                        'code' => 2,
                        'mag' => array(
                            'text' => $text,
                        ),
                    );

                }

            } else {
                $text .= "请重新上传图片";
                $info = array(
                    'code' => 0,
                    'error' => $info['Error'],
                    'mag' => array(
                        'text' => $text,
                    ),
                );
            }
            return json_encode($info);

    }


}