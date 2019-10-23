<?php

use think\migration\Migrator;
use think\migration\db\Column;

class JcdcMatch extends Migrator
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
        $table = $this->table('jcdc_match', ['engine' => 'InnoDB', 'comment' => '北京单场奖金指数表']);
        $table->addColumn('match_num', 'char', ['comment' => '比赛编号', 'limit' => 20, 'default' => ''])
            ->addColumn('sp_spf', 'text', ['comment' => '胜平负奖金指数'])
            ->addColumn('sp_rqspf', 'text', ['comment' => '让球胜平负奖金指数'])
            ->addColumn('sp_bf', 'text', ['comment' => '全场比分奖金指数'])
            ->addColumn('sp_jqs', 'text', ['comment' => '总进球数奖金指数'])
            ->addColumn('sp_bqc', 'text', ['comment' => '半全场奖金指数'])
            ->addColumn('sp_sxp', 'text', ['comment' => '上下盘单双数奖金指数'])
            ->addColumn('sp_spf_var', 'text', ['comment' => '胜平负奖金指数变化数据'])
            ->addColumn('sp_rqspf_var', 'text', ['comment' => '让球胜平负奖金指数变化数据'])
            ->addColumn('sp_bf_var', 'text', ['comment' => '全场比分奖金指数变化数据'])
            ->addColumn('sp_jqs_var', 'text', ['comment' => '总进球数奖金指数变化数据'])
            ->addColumn('sp_bqc_var', 'text', ['comment' => '半全场奖金指数变化数据'])
            ->addColumn('sp_sxp_var', 'text', ['comment' => '上下盘单双数奖金指数变化数据'])
            ->addColumn('create_at', 'datetime', ['comment' => '创建时间', 'null' => true])
            ->addColumn('update_at', 'datetime', ['comment' => '更新时间', 'null' => true])

            ->addIndex('match_num')
            ->create();
    }
}
