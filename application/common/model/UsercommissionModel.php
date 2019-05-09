<?php

namespace app\common\model;

use think\Db;
use think\Model;

class UsercommissionModel extends Model
{
    protected $table = "user_account";

    /**
     *   用户佣金
     * @param $param  条件参数
     * @transaction_type 交易类型：0-销售佣金，1-推荐佣金，2-支付订单, 3-退款, 4-提现,5-爱风尚迁移佣金,6-管理奖金,7-桃李奖金,8-拒绝提现,9-手动处理提现,10-提现通过, 11-礼包津贴&有效/超级VIP的津贴
     * @balance  余额         @operation_name  操作员名称         @description  描述，备注，存相关订单号，提现流水号，开店流水号等
     * @create_time  创建时间  @amount 交易金额，收入为正，支出为负
     * @income 累计收益        @outcome  累计支出     @total 累计佣金-afs  @pay 已提佣金-afs
     */
    public function commission($param)
    {
        $where = 'user_id=' . $param['id'] . ' and (transaction_type in(0,1,2,3,4,5,6,7,8))';
        $field = 'user_account_id, transaction_type, balance, operation_name, description, create_time, amount';
        $list = self::field($field)->where('user_id', $param['id'])->paginate('10')->toArray();
        $shopkeeper = ShopkeeperModel::field('income, outcome, total, pay')->where('user_id', $param['id'])->find();
        $handRefuse = 0;
        $withdrawAccess = 0;
        foreach ($list['data'] as $k => $v) {
            switch ($v['transaction_type']) {
                case 0:
                    $list['data'][$k]['transaction_type_name'] = "销售佣金";
                    break;
                case 1:
                    $list['data'][$k]['transaction_type_name'] = "推荐佣金";
                    break;
                case 2:
                    $list['data'][$k]['transaction_type_name'] = "支付订单";
                    break;
                case 3:
                    $list['data'][$k]['transaction_type_name'] = "退款";
                    break;
                case 4:
                    //amount > 0 是冻结账户删掉此数据
                    if ($v['amount'] < 0) {
                        $withdraw = WithdrawModel::get(['withdraw_id' => $v['description']]);
                        $str = "";
                        if ($withdraw) {
                            if ($withdraw->status == 2) $str = '-(已拒绝)';
                            else if ($withdraw->status == 3) $str = '-(已通过)';
                            else if ($withdraw->status == 4) $str = '-(手动处理)';
                            else if ($withdraw->status == 1) $str = '-(已审核)';
                            else $str = '(未处理)';
                        }
                        $list['data'][$k]['transaction_type_name'] = "申请提现" . $str;
                    } else {
                        unset($list['data'][$k]);
                    }
                    break;
                case 5:
                    $list['data'][$k]['transaction_type_name'] = "迁移佣金";
                    break;
                case 6:
                    $list['data'][$k]['transaction_type_name'] = "管理奖金";
                    break;
                case 7:
                    $list['data'][$k]['transaction_type_name'] = "桃李奖金";
                    break;
                case 8:
                    //amount < 0 是冻结账户删掉此数据
                    if ($v['amount'] > 0)
                        $list['data'][$k]['transaction_type_name'] = "拒绝提现";
                    else
                        unset($list['data'][$k]);
                    break;
                case 9:
                    $list['data'][$k]['transaction_type_name'] = "手动处理";
                    $handRefuse += abs($v['amount']);
                    break;
                case 10:
                    $list['data'][$k]['transaction_type_name'] = "提现通过";
                    $withdrawAccess += abs($v['amount']);
                    break;
                default:
                    break;

            }

        }

        $info = array(
            'income' => $shopkeeper['income'],
            'outcome' => $shopkeeper['outcome'],
            'total' => $shopkeeper['total'],
            'pay' => $shopkeeper['pay'],
            'data' => $list['data'],
            'hand_refuse' => sprintf('%.2f',$handRefuse),
            'withdraw_access' => sprintf('%.2f', $withdrawAccess),
        );


        return $info;
    }

}