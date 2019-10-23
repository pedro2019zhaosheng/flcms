<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Lottery extends Migrator
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
        $table = $this->table('lottery', array('engine' => 'InnoDB', 'comment' => '彩种表'));
        $table->addcolumn('name', 'string', array('default' => '', 'comment' => '彩种名称'))
            ->addColumn('code', 'string', array('default' => '', 'comment' => '彩种代码'))
            ->addColumn('img', 'string', array('default' => '', 'comment' => '图标'))
            ->addColumn('status', 'boolean', array('default' => 0, 'comment' => '1：停售'))
            ->addColumn('match', 'boolean', array('default' => 0, 'comment' => '赛事数据爬取状态  0：停止   1：开始'))
            ->addColumn('result', 'boolean', array('default' => 0, 'comment' => '爬取赛事结果  0：停止   1：开始'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('code', 'status', 'match', 'result'))
            ->create();
    }
}
