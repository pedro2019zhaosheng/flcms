<?php

use think\migration\Migrator;
use think\migration\db\Column;

class JcdcOpen extends Migrator
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
        $table = $this->table('jcdc_open', ['engine' => 'InnoDB', 'comment' => '北京单场赛事结果表']);
        $table->addColumn('match_num', 'string', ['default' => '', 'comment' => '比赛编号'])
            ->addColumn('half_score', 'char', ['default' => '', 'comment' => '半场比分'])
            ->addColumn('total_score', 'char', ['default' => '', 'comment' => '全场比分（含加时赛比分）, 格式: 主:客'])
            ->addColumn('normal_score', 'char', ['default' => '', 'comment' => '90分钟比分, 格式: 主:客'])
            ->addColumn('kick_score', 'char', ['default' => '', 'comment' => '点球比分, 格式: 主:客'])
            ->addColumn('status', 'boolean', ['default' => 0, 'comment' => '是否已开奖, 1已开奖'])
            ->addColumn('create_at', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('match_num')
            ->addIndex('status')
            ->addIndex('create_at')
            ->create();
    }
}
