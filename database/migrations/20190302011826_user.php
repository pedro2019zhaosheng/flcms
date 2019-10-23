<?php

use think\migration\Migrator;
use think\migration\db\Column;

class User extends Migrator
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
     *
     */
    public function change()
    {
        $table = $this->table('member', array('engine' => 'InnoDB', 'comment' => '会员表'));
        $table->addColumn('username', 'biginteger', array('limit'=>'16','default' => 0,'null' => false, 'comment' => '用户名，登陆使用'))
            ->addcolumn('chn_name', 'string', array('limit' => 50, 'default' => '','null' => false, 'comment' => '真实姓名'))
            ->addColumn('password', 'string', array('limit' => 255, 'default' => '','null' => false, 'comment' => '用户密码'))
            ->addColumn('top_id', 'integer', array('limit' => 11, 'default' => 0,'null' => false, 'comment' => '上级ID'))
            ->addcolumn('photo', 'string', array('limit'=>255,'default' => '', 'comment' => '头像'))
            ->addColumn('balance', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '账号余额'))
            ->addColumn('frozen_capital', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '冻结资金'))
            ->addColumn('withdraw_deposit', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '提现资金'))
            ->addColumn('hadsel', 'decimal', array('precision' => 18, 'scale' => 2, 'default' => "0.00",'null' => false, 'comment' => '彩金'))
            ->addcolumn('id_card', 'string', array('limit'=>'18','default' => '','null' => false, 'comment' => '身份证号'))
            ->addcolumn('role', 'boolean', array('limit'=>2,'default' => '0','null' => false, 'comment' => '身份  1：会员   2：代理商'))
            ->addColumn('is_moni', 'boolean', array('limit' => 1, 'default' => 1,'null' => false, 'comment' => '状态  0：模拟   1：真实'))
            ->addcolumn('frozen', 'boolean', array('limit' => 1, 'default' => 0,'null' => false, 'comment' => '状态  0：冻结   1：启用'))
            ->addcolumn('real_status', 'boolean', array('limit' => 1, 'default' => 0,'null' => false, 'comment' => '是否实名认证 0：否  1：是'))
            ->addColumn('is_return_money', 'boolean', array('limit' => 1, 'default' => 0,'null' => false, 'comment' => '是否允许提现 0否 1是'))
            ->addColumn('dev_status', 'boolean', array('limit' => 1, 'default' => 0,'null' => false, 'comment' => '是否允许发展下级  0：否  1：是'))
            ->addColumn('create_at', 'datetime', array('comment' => '创建时间', 'null' => true))
            ->addColumn('last_login_time', 'datetime', array('comment' => '最后登录时间', 'null' => true))
            ->addColumn('last_login_ip', 'char', array('limit' => 20, 'default' => '', 'comment' => '最后登录IP'))
            ->addColumn('is_delete', 'boolean', array('limit' => 2, 'default' => 0,'null' => false, 'comment' => '删除状态，1已删除'))
            ->addColumn('delete_time', 'datetime', array('comment' => '删除时间', 'null' => true))
            ->addColumn('update_at', 'datetime', array('comment' => '修改时间', 'null' => true))
            ->addcolumn('agent_invite_code', 'char', array('limit' => 6, 'default' => '', 'comment' => '邀请码'))
            ->addColumn('top_username', 'biginteger', array('default' => 0, 'comment' => '上级账号'))
            ->addIndex(array('username'), array('unique' => true))
            ->addIndex(array('role', 'top_id', 'frozen', 'is_delete'))
            ->create();
    }
}
