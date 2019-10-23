<?php

use think\migration\Migrator;
use think\migration\db\Column;

class JczqBase extends Migrator
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
        $this->execute('ALTER TABLE ' . config('database.prefix') . 'jczq_match COMMENT "竞彩足球赛事奖金指数表"');
        $table = $this->table('jczq_base', ['engine' => 'InnoDB', 'comment' => '竞彩足球赛事基本信息表']);
        $table->addColumn('match_num', 'char', ['limit' => 20, 'default' => '', 'comment' => '比赛编号'])
            ->addColumn('league_num', 'string', ['comment' => '联赛编号', 'default' => ''])
            ->addColumn('league_name', 'string', ['comment' => '联赛名称', 'default' => ''])
            ->addColumn('host_name', 'string', ['comment' => '主队名称', 'default' => ''])
            ->addColumn('host_num', 'string', ['comment' => '主队编号', 'default' => ''])
            ->addColumn('host_icon', 'string', ['comment' => '主队图标', 'default' => ''])
            ->addColumn('guest_name', 'string', ['comment' => '客队名称', 'default' => ''])
            ->addColumn('guest_num', 'string', ['comment' => '客队编号', 'default' => ''])
            ->addColumn('guest_icon', 'string', ['comment' => '客队图标', 'default' => ''])
            ->addColumn('start_time', 'biginteger', ['comment' => '开赛时间', 'default' => 0])
            ->addColumn('match_time', 'biginteger', ['comment' => '比赛时间', 'default' => 0])
            ->addColumn('jc_num', 'string', ['limit' => 20, 'comment' => '竞彩编号', 'default' => ''])
            ->addColumn('isAh', 'boolean', ['comment' => '竞彩赛程与 SportsDT 对阵是否相反，0 相同 1 相反', 'default' => 0])
            ->addColumn('sale_status', 'boolean', ['comment' => '出售状态, 1 出售中', 'default' => 0])
            ->addColumn('match_status', 'boolean', ['comment' => '比赛状态,0正常 1取消 2延期 3腰斩', 'default' => 0])
            ->addColumn('cutoff_time', 'datetime', ['comment' => '手动截止时间', 'null' => true])
            ->addColumn('sys_cutoff_time', 'datetime', ['comment' => '系统截止时间', 'null' => true])
            ->addIndex('match_num')
            ->addIndex('sale_status')
            ->addIndex('isAh')
            ->addIndex('match_status')
            ->addIndex('cutoff_time')
            ->addIndex('sys_cutoff_time')
            ->save();
    }
}
