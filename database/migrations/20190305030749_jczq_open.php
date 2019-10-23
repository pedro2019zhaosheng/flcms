<?php

use think\migration\Migrator;
use think\migration\db\Column;

class JczqOpen extends Migrator
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
        $table = $this->table('jczq_open', array('engine' => 'InnoDB', 'comment' => '竞彩足球开奖表'));
        $table->addcolumn('match_time', 'biginteger', array('default' => 0, 'comment' => '赛事时间'))
            ->addColumn('start_time', 'biginteger', array('default' => 0, 'comment' => '开赛时间'))
            ->addColumn('match_num', 'string', array('default' => '', 'comment' => '赛事编号'))
            ->addColumn('host_team', 'string', array('default' => '', 'comment' => '主队名称'))
            ->addColumn('guest_team', 'string', array('default' => '', 'comment' => '客队名称'))
            ->addColumn('half_score', 'char', array('limit' => 10, 'default' => '', 'comment' => '半场比分'))
            ->addColumn('total_score', 'char', array('limit' => 10, 'default' => '', 'comment' => '全场比分'))
            ->addColumn('rqs', 'char', array('limit' => 10, 'default' => '', 'comment' => '让球数'))
            ->addColumn('sp_spf', 'char', array('limit' => 10, 'default' => '', 'comment' => '胜平负胜率'))
            ->addColumn('sp_rqspf', 'char', array('limit' => 10, 'default' => '', 'comment' => '让球胜平负胜率'))
            ->addColumn('sp_bf', 'char', array('limit' => 10, 'default' => '', 'comment' => '比分胜率'))
            ->addColumn('sp_jqs', 'char', array('limit' => 10, 'default' => '', 'comment' => '进球数胜率'))
            ->addColumn('sp_bqc', 'char', array('limit' => 10, 'default' => '', 'comment' => '半全场胜率'))
            ->addColumn('spf', 'char', array('limit' => 10, 'default' => '', 'comment' => '胜平负胜率'))
            ->addColumn('rqspf', 'char', array('limit' => 10, 'default' => '', 'comment' => '让球胜平负胜率'))
            ->addColumn('bf', 'char', array('limit' => 10, 'default' => '', 'comment' => '比分胜率'))
            ->addColumn('jqs', 'char', array('limit' => 10, 'default' => '', 'comment' => '进球数胜率'))
            ->addColumn('bqc', 'char', array('limit' => 10, 'default' => '', 'comment' => '半全场胜率'))
            ->addColumn('status', 'boolean', array('default' => 0, 'comment' => '状态'))
            ->addColumn('type', 'boolean', array('default' => 0, 'comment' => '开奖类型'))
            ->addColumn('create_at', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addColumn('update_at', 'datetime', array('null' => true, 'comment' => '更新时间'))
            ->addIndex(array('match_num', 'status', 'type', 'match_time', 'start_time'))
            ->create();
    }
}
