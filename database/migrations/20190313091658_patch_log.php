<?php

use think\migration\Migrator;
use think\migration\db\Column;

class PatchLog extends Migrator
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
        $table = $this->table('patch_log', array('engine' => 'InnoDB', 'comment' => '彩种爬取日志'));
        $table->addColumn('name', 'char', array('default' => '', 'comment' => '彩种名称'))
            ->addColumn('status', 'boolean', array('default' => 1, 'comment' => '爬取状态, 0：失败'))
            ->addcolumn('info', 'text', array('comment' => '错误信息'))
            ->addcolumn('desc', 'string', array('default' => '', 'comment' => '描述'))
            ->addColumn('date', 'datetime', array('null' => true, 'comment' => '爬取时期'))
            ->create();
    }
}
