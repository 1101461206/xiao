<?php
namespace app\admin\controller;
use think\helper\Time;

use app\common\model\IndexadminModel as indexadmin;

class IndexController extends AdminController
{
    public function indexAction()
    {
        list($start, $end) = Time::month();

        $indexadmin=new indexadmin();
        //已售罄商品
        $sell_out=$indexadmin::name('goods')
                   ->alias('g')
                   ->join('goods_standard gs', 'g.goods_id=gs.goods_id')
                   ->where('g.is_delete',0)
                   ->where('g.is_on_sale',1)
                   ->where('gs.stock',0)
                   ->count();
        //库存预警
        $warning_sql=$indexadmin::name('goods')
                   ->field('gs.warn_number,gs.goods_standard_id')
                   ->alias('g')
                   ->join('goods_standard gs','g.goods_id=gs.goods_id')
                   ->where('g.is_delete',0)
                   ->where('g.is_on_sale',1)
                   ->buildSql();
        $warning=$indexadmin::table($warning_sql.'t')
                  ->join('goods_standard gs','t.goods_standard_id=gs.goods_standard_id')
                  ->where('gs.stock','<=','t.warn_number')
                  ->count();

        //待付款订单
        $payment=$indexadmin::name('order_info')
                 ->where('pay_status',0)
                 ->count();
        //维权订单
        $refund=$indexadmin::name('order_info')
                ->where('apply_refund_time',">","0")
                ->where('refund_status',"=","1")
                ->count();



        //本月新增队长
        $user_num=$indexadmin::name('user')
                 ->where('agent_time','>=',date('Y-m-d',$start))
                 ->where('agent_time','<=',date('Y-m-d',$end))
                 ->count();

        //本月销售和订单
        $order_num=$indexadmin::name('order_info')
                ->field('sum(goods_amount) as pirce')
                ->field('count(order_info_id) as num')
                ->where('pay_status','=','2')
                ->where('add_time','>=',$start)
                ->where('add_time','<=',$end)
                ->find();

        $data=array(
            'user_num'=>$user_num,
            'order_num'=>$order_num->num,
            'order_price'=>$order_num->pirce,
            'sell_out'=>$sell_out,
            'wrning'=>$warning,
            'payment'=>$payment,
            'refund'=>$refund,
        );

        $this->assign('data',$data);
        return $this->fetch('index/index');
    }
}
