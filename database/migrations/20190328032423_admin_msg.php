<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AdminMsg extends Migrator
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
        $table = $this->table('admin_msg', ['engine' => 'InnoDB', 'comment' => '后台消息表']);
        $table->addColumn('name', 'string', ['default' => '', 'comment' => '用户昵称或名称'])
            ->addColumn('account', 'string', ['default' => '', 'comment' => '用户账号'])
            ->addColumn('msg_type', 'boolean', ['limit' => 2, 'default' => 1, 'comment' => '消息类型, 1:系统消息  2:代理商消息 3:会员消息 4:其他'])
            ->addColumn('body_type', 'boolean', ['limit' => 2, 'default' => 1, 'comment' => '内容类型, 1:资金提现 2:会员注单 3:资金充值 4:其他'])
            ->addColumn('desc', 'string', ['default' => '', 'comment' => '消息简介'])
            ->addColumn('icon', 'integer', ['default' => 0, 'comment' => '头像/icon 附件ID'])
            ->addColumn('send_time', 'datetime', ['null' => true, 'comment' => '消息发送时间'])
            ->addColumn('read_state', 'boolean', ['limit' => 1, 'default' => 0, 'comment' => '消息状态, 0:未读 1:已读'])
            ->addIndex('account')
            ->addIndex('msg_type')
            ->addIndex('body_type')
            ->addIndex('read_state')
            ->save();
    }
}
