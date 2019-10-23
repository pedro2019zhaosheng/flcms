<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminLoginlog extends Migrator
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
       /* $table = $this->table('admin_loginlog', array('id'=>'loginid','engine' => 'MyISAM', 'comment' => '后台登陆日志表'));
        $table->addColumn('username', 'string', array('default' =>'','comment' => '登录帐号'))
            ->addColumn('logintime', 'datetime', array('null' => true, 'comment' => '登录时间'))
            ->addcolumn('loginip', 'char', array('limit' => 50,'default'=>'', 'comment' => '登录IP'))
            ->addColumn('status', 'boolean', array('limit'=>4,'null' => false,'default'=>0, 'comment' => '状态,1为登录成功，0为登录失败'))
            ->addColumn('password', 'char', array('limit'=>30,'default' =>'', 'comment' => '尝试错误密码'))
            ->addColumn('info', 'string', array('default' =>'0','comment' => '其他说明'))
            ->create();*/
    }
}
