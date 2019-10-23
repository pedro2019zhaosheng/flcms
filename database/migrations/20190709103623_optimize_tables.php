<?php

use think\migration\Migrator;
use think\migration\db\Column;

class OptimizeTables extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change()
    {
        // 优化admin索引
        $table = $this->table('admin');
        $table->removeIndex(['login_status', 'is_delete'])
            ->addIndex('login_status')
            ->addIndex('is_delete')
            ->update();

        // 优化地域索引
        $table = $this->table('admin_area');
        $table->removeIndex(['pid', 'sort'])
            ->addIndex('pid')
            ->update();

        // 优化彩种表
        $table = $this->table('lottery');
        $table->removeIndex(['code', 'status', 'match', 'result'])
            ->addIndex('code')
            ->addIndex('status')
            ->addIndex('is_run')
            ->update();

        // 优化会员银行卡表
        $table = $this->table('member_bank');
        $table->removeIndex(['member_id', 'bank_code', 'bank_num', 'status'])
            ->addIndex('member_id')
            ->addIndex('bank_code')
            ->addIndex('status')
            ->update();

        // 优化会员返佣比例表
        $table = $this->table('member_ratio');
        $table->removeIndex(['member_id', 'lottery_id', 'status'])
            ->addIndex('member_id')
            ->addIndex('lottery_id')
            ->addIndex('status')
            ->update();

        // 优化订单表
        $table = $this->table('order');
        $table->removeIndex(['member_id', 'lottery_id', 'status', 'is_moni'])
            ->addIndex('member_id')
            ->addIndex('status')
            ->update();

        // 优化订单内容表
        $table = $this->table('order_content');
        $table->removeIndex(['lottery_id', 'status', 'order_id'])
            ->addIndex('lottery_id')
            ->addIndex('status')
            ->addIndex('order_id')
            ->update();

        // 优化订单详情表
        $table = $this->table('order_detail');
        $table->removeIndex(['lottery_id', 'match_num', 'order_content_id', 'status'])
            ->addIndex('order_content_id')
            ->addIndex('match_num')
            ->addIndex('status')
            ->addIndex('order_id')
            ->update();
    }
}
