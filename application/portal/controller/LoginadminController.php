<?php

namespace app\portal\controller;

use app\common\model\UseradminModel as useradmin;
use think\Controller;
use think\Request;
use think\facade\Config;
use think\facade\Session;

class loginadminController extends Controller
{
    public function loginAction()
    {
        $param = request()->post();
        $usermodel = new useradmin();
        if(!captcha_check($param['verification'])){ // 验证码检测 验证失败
            return apiReturn(503,'验证码不正确');
        }
        $userInfo = $usermodel->where('user_name', $param['name'])->find();
        if (empty($userInfo)) {// 验证码用户是否存在
            return apiReturn(501, '用户或密码错误');
        }
        if ($userInfo['status'] != 1) {//验证账户状态
            return apiReturn(501, '状态异常');
        }
        $md5_key = Config::get('md5_key');
        $md5_password = sha1($param['pwd'] . '-' . $userInfo['salt'] . '-' . $md5_key);
        if ($userInfo['password'] != $md5_password) { // 验证用户密码是否正确
            return apiReturn(502, '密码不正确');
        }

        // 验证通过 登陆成功 跳转系统首页
        Session::set('admin_info',$userInfo);
        return apiReturn(0,'登录成功');

    }
}
