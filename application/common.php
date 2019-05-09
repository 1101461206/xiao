<?php
use think\facade\Session;

// 应用公共文件
//返回信息 json格式
function apiReturn($errorCode, $errorMessage, $data = null, $count = 0) {
    return json([
        'code'  => $errorCode,
        'msg'   => $errorMessage,
        'data'  => $data,
        'count' => $count,
    ], 200);
}

function GetSessionData($name){
    return Session::get($name);
}


