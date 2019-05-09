<?php
namespace app\admin\controller;
use think\helper\Time;

use app\common\model\OrderadminModel as orderadmin;

class OrderController extends AdminController
{
    public function listAction()
    {
        $orderadmin=new orderadmin();
        //全部订单
        $list=$orderadmin::name('order_info i')
                ->join('user u','u.user_id=i.user_id')
                ->join('admin ad','i.kfuser_id=ad.admin_id')
                ->field('i.order_info_id,i.order_sn,u.nick_name,i.goods_amount,i.province_name,i.city_name,i.district_name,i.address,i.telephone,i.pay_name,pay_status,i.consignee,i.order_status,ad.name,i.prepare_status,i.add_time')
                ->order('i.order_info_id','desc')
                ->paginate(10)
                ->each(function($item,$key){
                    $orderadmin=new orderadmin();
                    $order_goods=$orderadmin::name('order_goods g')
                        ->join('goods gs',"g.goods_id=gs.goods_id")
                        ->field('g.goods_name,g.goods_number,g.shop_price,gs.goods_thumb,g.standard_name')
                        ->where('g.order_info_id','=',$item['order_info_id'])
                        ->select();
                    $item->goods_name=$order_goods;
                    if($item['pay_name']=="weixin"){
                        $item['pay_name']="微信";
                    }else if($item['pay_name']=="surplus"){
                        $item['pay_name']="余额";
                    }

                    if($item['pay_status']==0){
                        $item['pay_status']="未支付";
                    }else if($item['pay_status']==1){
                        $item['pay_status']="支付中";
                    }else if($item['pay_status']==2){
                        $item['pay_status']="已付款";
                    }

                    if($item['order_status']==0){
                        $item['order_status']="未确认";
                    }else if($item['order_status']==1){
                        $item['order_status']="已确认";
                    }else if($item['order_status']==2){
                        $item['order_status']="已取消";
                    }else if($item['order_status']==3){
                        $item['order_status']="审核中";
                    }else if($item['order_status']==4){
                        $item['order_status']="退款中";
                    }else if($item['order_status']==5){
                        $item['order_status']="已退款";
                    }else if($item['order_status']==6){
                        $item['order_status']="拒绝退款";
                    }else if($item['order_status']==7){
                        $item['order_status']="申请退货";
                    }else if($item['order_status']==8){
                        $item['order_status']="退货中";
                    }else if($item['order_status']==9){
                        $item['order_status']="完成退货";
                    }else if($item['order_status']==10){
                        $item['order_status']="拒绝退货";
                    }

                    if($item['prepare_status']==0){
                        $item['prepare_status']="未备货";
                    }else if($item['prepare_status']==1){
                        $item['prepare_status']="备货中";
                    }


                });
//        echo "<pre>";
//        var_dump($list);
//        echo "<pre>";
//        exit;
        $count=$list->total();
        $page=$list->render();


        $this->assign('list',$list);
        $this->assign('page',$page);
        return $this->fetch('order/index');
    }
}
