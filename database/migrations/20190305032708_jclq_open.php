<?php

use think\migration\Migrator;
use think\migration\db\Column;

class JclqOpen extends Migrator
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
        $table = $this->table('jclq_open', array('engine' => 'InnoDB', 'comment' => '竞彩篮球开奖表'));
        $table->addcolumn('match_time', 'biginteger', array('default' => 0, 'comment' => '赛事时间'))
            ->addColumn('start_time', 'biginteger', array('default' => 0, 'comment' => '开赛时间'))
            ->addColumn('match_num', 'char', array('limit' => 10, 'default' => '', 'comment' => '赛事编号'))
            ->addColumn('host_team', 'char', array('limit' => 20, 'default' => '', 'comment' => '主队名称'))
            ->addColumn('guest_team', 'char', array('limit' => 20, 'default' => '', 'comment' => '客队名称'))
            ->addColumn('first_score', 'string', array('default' => '', 'comment' => '第一节比分'))
            ->addColumn('second_score', 'string', array('default' => '', 'comment' => '第二节比分'))
            ->addColumn('third_score', 'string', array('default' => '', 'comment' => '第三节比分'))
            ->addColumn('fourth_score', 'string', array('default' => '', 'comment' => '第四节比分'))
            ->addColumn('add_time_score', 'string', array('default' => '', 'comment' => '加时比分'))
            ->addColumn('total_score', 'string', array('default' => '', 'comment' => '全场比分'))
            ->addColumn('rfs', 'char', array('limit' => 10, 'default' => '', 'comment' => '让分数'))
            ->addColumn('sp_rfsf', 'char', array('limit' => 10, 'default' => '', 'comment' => '让分胜负胜率'))
            ->addColumn('sp_dxf', 'char', array('limit' => 10, 'default' => '', 'comment' => '大小分胜率'))
            ->addColumn('sp_sfc', 'char', array('limit' => 10, 'default' => '', 'comment' => '胜负差胜率'))
            ->addColumn('sf', 'char', array('limit' => 10, 'default' => '', 'comment' => '胜平负胜率'))
            ->addColumn('rfsf', 'char', array('limit' => 10, 'default' => '', 'comment' => '让球胜平负胜率'))
            ->addColumn('dxf', 'char', array('limit' => 10, 'default' => '', 'comment' => '比分胜率'))
            ->addColumn('sfc', 'char', array('limit' => 10, 'default' => '', 'comment' => '进球数胜率'))
            ->addColumn('status', 'boolean', array('default' => 0, 'comment' => '状态'))
            ->addColumn('type', 'boolean', array('default' => 0, 'signed' => 'unsigned', 'comment' => '开奖类型'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('match_time', 'start_time', 'match_num', 'type', 'status'))
            ->create();
    }
}
