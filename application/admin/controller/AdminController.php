<?php
namespace app\admin\controller;
use think\Controller;
use think\facade\Session;



class AdminController extends Controller
{
    protected $merchant_id;
    public function initialize()
    {
        $userinfo=Session::get('admin_info');
        if(empty($userinfo)){
            $this->redirect('admin/login/index');
        }

        $this->assign('admin_name',$userinfo['user_name']);
    }
}
