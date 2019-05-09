<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Config;
use think\Session;
use think\captcha\Captcha;



class LoginController extends Controller
{
    public function indexAction()
    {
        return $this->fetch('index');

    }


    public function verifyAction(){
        $captcha= new Captcha();
        return $captcha->entry();
    }
}
