<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJczqOpen extends Migrator
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
        $table = $this->table('jczq_open');
        $table->removeColumn('match_time')
            ->removeColumn('start_time')
            ->removeColumn('host_team')
            ->removeColumn('guest_team')
            ->removeColumn('sp_spf')
            ->removeColumn('sp_rqspf')
            ->removeColumn('sp_bf')
            ->removeColumn('sp_jqs')
            ->removeColumn('sp_bqc')
            ->removeColumn('spf')
            ->removeColumn('rqspf')
            ->removeColumn('bf')
            ->removeColumn('jqs')
            ->removeColumn('bqc')
            ->removeColumn('type')
            ->removeIndex(['start_time', 'match_time', 'type'])
            ->changeColumn('match_num', 'string', ['comment' => '比赛编号', 'default' => ''])
            ->changeColumn('status', 'boolean', ['limit' => 2, 'default' => 0, 'comment' => '是否已开奖, 1已开奖'])
            ->changeColumn('total_score', 'char', ['limit' => 10, 'default' => '', 'comment' => '全场比分（含加时赛比分）, 格式: 主:客'])
            ->addColumn('normal_score', 'char', ['limit' => 10, 'default' => '', 'comment' => '90分钟比分, 格式: 主:客'])
            ->addColumn('kick_score', 'char', ['limit' => 10, 'default' => '', 'comment' => '点球比分, 格式: 主:客'])
            ->update();
    }
}
