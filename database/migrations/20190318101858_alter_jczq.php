<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJczq extends Migrator
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
        $table = $this->table('jczq');
        $table->rename('jczq_match');
        $table->changeColumn('sp_spf', 'text', array('comment' => '胜平负奖金指数'))
            ->changeColumn('sp_rqspf', 'text', array('comment' => '让球胜平负奖金指数'))
            ->changeColumn('sp_bf', 'text', array('comment' => '全场比分奖金指数'))
            ->changeColumn('sp_jqs', 'text', array('comment' => '总进球数奖金指数'))
            ->changeColumn('sp_bqc', 'text', array('comment' => '半全场奖金指数'))
            ->changeColumn('match_num', 'char', array('limit' => 20, 'default' => '', 'comment' => '比赛编号'))
            ->changeColumn('rqspf_single', 'boolean', array('comment' => '让球单关', 'limit' => 2, 'default' => 0))
            ->addColumn('sp_spf_var', 'text', array('comment' => '胜平负奖金指数变化数据'))
            ->addColumn('sp_rqspf_var', 'text', array('comment' => '让球胜平负奖金指数变化数据'))
            ->addColumn('sp_bf_var', 'text', array('comment' => '全场比分奖金指数变化数据'))
            ->addColumn('sp_jqs_var', 'text', array('comment' => '总进球数奖金指数变化数据'))
            ->addColumn('sp_bqc_var', 'text', array('comment' => '半全场奖金指数变化数据'))
            ->removeColumn('open_step')
            ->removeColumn('status')
            ->removeColumn('cutoff_time')
            ->removeColumn('match_time')
            ->removeColumn('start_time')
            ->removeColumn('match_day')
            ->removeColumn('match_name')
            ->removeColumn('host_team')
            ->removeColumn('guest_team')
            ->removeIndex(['match_time', 'start_time'])
            ->update();
    }
}
