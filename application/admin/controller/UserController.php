<?php
namespace app\admin\controller;
use think\helper\Time;
use app\common\model\UsercommissionModel as commission;

use app\common\model\UserModel as user;

class UserController extends AdminController
{

    /**
     * @return mixed
     * @throws \think\exception\DbException
     * 用户列表首页
     */
    public function indexAction()
    {
        $user=new user();
        $list=$user::name('user u')
            ->join('user us','us.user_id=u.parent_id')
            ->join('admin ad','ad.admin_id=u.kfuser')
            ->field('u.user_id,u.head_image_url,u.telephone,u.nick_name,u.unionid,u.user_role,us.nick_name as p_name,u.openid,u.unionid,u.create_time,u.is_subscribe,ad.name as kfuser')
            ->order('u.user_id','desc')
            ->paginate('20');
        $count=$list->total();
        $page=$list->render();
        $this->assign('page',$page);
        $this->assign('list',$list);
        return $this->fetch('user/index');
    }

    public function commissionAction(){
        $param=request()->param();
        $commission=new commission();
        $commission=$commission->commission($param);
        $this->assign('commission',$commission);
        return$this->fetch('user/commission');
    }

}
