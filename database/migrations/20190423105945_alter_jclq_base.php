<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJclqBase extends Migrator
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
        if ($this->hasTable('jclq')) {
            $this->dropTable('jclq');
        }

        $table = $this->table('jclq_base', ['engine' => 'InnoDB', 'comment' => '竞彩篮球基本信息表']);
        $table->addColumn('match_num', 'char', ['limit' => 20, 'comment' => '赛事编号', 'default' => ''])
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
            ->addColumn('jc_num', 'string', ['comment' => '竞彩编号', 'default' => ''])
            ->addColumn('sale_status', 'boolean', ['comment' => '出售状态, 0:已停售  1:出售中', 'default' => 1])
            ->addColumn('match_status', 'boolean', ['comment' => '比赛状态,0正常 1取消 2延期 3腰斩 4待定', 'default' => 0])
            ->addColumn('cutoff_time', 'datetime', ['comment' => '手动截止时间', 'null' => true])
            ->addColumn('sys_cutoff_time', 'datetime', ['comment' => '系统截止时间', 'null' => true])
            ->addColumn('rqs', 'char', ['comment' => '让球数', 'default' => ''])
            ->addColumn('jc_date', 'datetime', ['comment' => '竞彩日期', 'null' => true])
            ->addIndex('match_num')
            ->addIndex('start_time')
            ->addIndex('sale_status')
            ->addIndex('match_status')
            ->addIndex('jc_date')
            ->create();
    }
}
