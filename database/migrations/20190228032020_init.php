<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Init extends Migrator
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
        // create the table
        $table = $this->table('admin', array('engine' => 'InnoDB', 'comment' => '总后台管理员表'));
        $table->addColumn('username', 'string', array('limit' => 15, 'default' => '', 'comment' => '用户名，登陆使用'))
            ->addColumn('password', 'char', array('limit' => 32, 'default' => '', 'null' => false, 'comment' => '用户密码'))
            ->addColumn('email','string',array('limit'=>64,'default'=>'','null'=>false,'comment'=>'邮箱地址'))
            //->addColumn('email_bind','boolean',array('limit'=>1,'default'=>'0','comment'=>'是否绑定邮箱地址'))
            ->addColumn('login_status', 'boolean', array('limit' => 1, 'default' => 0, 'comment' => '登陆状态, 0不在线 ，1在线'))
            ->addColumn('last_login_ip', 'char', array('limit' => 20, 'default' => '', 'comment' => '最后登录IP'))
            ->addColumn('last_login_time', 'datetime', array('comment' => '最后登录时间', 'null' => true))
            ->addColumn('is_delete', 'boolean', array('limit' => 1, 'default' => 0, 'comment' => '删除状态，1已删除'))
            ->addIndex(array('username'), array('unique' => true))
            ->addIndex(array('login_status', 'is_delete'))
            ->create();
    }
}
