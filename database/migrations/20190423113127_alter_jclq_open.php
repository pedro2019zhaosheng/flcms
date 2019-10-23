<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJclqOpen extends Migrator
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
        if ($this->hasTable('jclq_open')) {
            $this->dropTable('jclq_open');
        }

        $this->table('jclq_open', ['engine' => 'InnoDB', 'comment' => '竞彩篮球开奖表'])
            ->addColumn('match_num', 'string', ['default' => '', 'comment' => '赛事编号'])
            ->addColumn('host_score', 'char', ['default' => '', 'comment' => '主队全场得分(不含加时)'])
            ->addColumn('guest_score', 'char', ['default' => '', 'comment' => '客队全场得分(不含加时)'])
            ->addColumn('status', 'boolean', ['default' => 0, 'comment' => '是否已开奖, 1已开奖'])
            ->addColumn('create_at', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_at', 'datetime', ['null' => true, 'comment' => '更新时间'])
            ->addIndex('match_num')
            ->addIndex('status')
            ->save();
    }
}
