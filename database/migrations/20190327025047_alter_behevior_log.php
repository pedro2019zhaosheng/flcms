<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterBeheviorLog extends Migrator
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
        $this->dropTable('admin_log');
        $table = $this->table('admin_log', array('engine' => 'InnoDB', 'comment' => '系统日志'));
        $table->addColumn('belong', 'boolean', ['default' => 1, 'comment' => '所属终端, 1: 总后台  2: 代理商后台  3: APP'])
            ->addColumn('executor', 'string', ['default' => '', 'comment' => '执行人(执行人账号或系统)'])
            ->addColumn('work_name', 'string', ['default' => '', 'comment' => '业务名称, 如:后台登录'])
            ->addColumn('remark', 'string', ['default' => '', 'comment' => '描述或备注'])
            ->addColumn('info', 'text', ['comment' => '日志信息'])
            ->addColumn('status', 'boolean', ['default' => 0, 'comment' => '执行状态, 0: 失败  1:成功'])
            ->addColumn('exec_time', 'datetime', ['null' => true, 'comment' => '执行时间'])
            ->addIndex('belong')
            ->addIndex('executor')
            ->addIndex('status')
            ->addIndex('exec_time')
            ->save();
    }
}
