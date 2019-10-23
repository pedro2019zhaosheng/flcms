<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Jclq extends Migrator
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
        $table = $this->table('jclq', array('engine' => 'InnoDB', 'comment' => '竞彩篮球赛事表'));
        $table->addcolumn('match_time', 'biginteger', array('default' => 0, 'comment' => '赛事时间'))
            ->addColumn('start_time', 'biginteger', array('default' => 0, 'comment' => '开赛时间'))
            ->addColumn('match_num', 'char', array('limit' => 10, 'default' => '', 'comment' => '赛事编号'))
            ->addColumn('match_day', 'char', array('limit' => 10, 'default' => '', 'comment' => '赛事星期几'))
            ->addColumn('match_name', 'char', array('limit' => 10, 'default' => '', 'comment' => '赛事名称'))
            ->addColumn('host_team', 'char', array('limit' => 20, 'default' => '', 'comment' => '主队名称'))
            ->addColumn('guest_team', 'char', array('limit' => 20, 'default' => '', 'comment' => '客队名称'))
            ->addColumn('sp_sf', 'text', array('comment' => '胜负胜率'))
            ->addColumn('sp_rfsf', 'text', array( 'comment' => '让分胜负胜率'))
            ->addColumn('sp_sfc', 'text', array('comment' => '胜负差胜率'))
            ->addColumn('sp_dxf', 'text', array('comment' => '大小分胜率'))
            ->addColumn('single', 'boolean', array('default' => 0, 'comment' => '单关'))
            ->addColumn('open_step', 'boolean', array('default' => 0, 'comment' => '开奖步骤'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('match_time', 'start_time', 'match_num', 'single'))
            ->create();
    }
}
