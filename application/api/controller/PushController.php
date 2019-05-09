<?php

namespace app\api\controller;

use app\common\model\UseradminModel as useradmin;
use think\Controller;
use think\Request;
use think\facade\Config;
use think\facade\Session;

class PushController extends ApiController
{
    public function IndexAction()
    {

        $signature = request()->get('signature');
        $timestamp = request()->get('timestamp');
        $nonce = request()->get('nonce');
        $echostr = request()->get('echostr');
        $token=config('wx_token');
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode( $tmpArr );
        $tmpStr = sha1( $tmpStr );
        if ($tmpStr == $signature ) {
            echo $echostr;
        } else {
            echo false;
        }
    }
}
