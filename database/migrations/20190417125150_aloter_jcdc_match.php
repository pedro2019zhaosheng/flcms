<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AloterJcdcMatch extends Migrator
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
        $table = $this->table('jcdc_match');
        $table->changeColumn('sp_spf_var', 'text', ['comment' => '胜平负奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->changeColumn('sp_rqspf_var', 'text', ['comment' => '让球胜平负奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->changeColumn('sp_bf_var', 'text', ['comment' => '全场比分奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->changeColumn('sp_jqs_var', 'text', ['comment' => '总进球数奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->changeColumn('sp_bqc_var', 'text', ['comment' => '半全场奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->changeColumn('sp_sxp_var', 'text', ['comment' => '上下盘单双数奖金指数变化数据', 'limit' => \Phinx\Db\Adapter\MysqlAdapter::TEXT_LONG])
            ->update();
    }
}
