<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateJclqMatch extends Migrator
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
        $this->table('jclq_match', ['engine' => 'InnoDB', 'comment' => '竞彩篮球奖金指数表'])
            ->addColumn('match_num', 'string', ['comment' => '赛事编号', 'default' => ''])
            ->addColumn('sp_sf', 'text', ['comment' => '胜负奖金指数'])
            ->addColumn('sp_rfsf', 'text', ['comment' => '让分胜负奖金指数'])
            ->addColumn('sp_sfc', 'text', ['comment' => '胜分差奖金指数'])
            ->addColumn('sp_dxf', 'text', ['comment' => '大小分奖金指数'])
            ->addColumn('sp_sf_var', 'text', ['comment' => '胜负奖金指数变化'])
            ->addColumn('sp_rfsf_var', 'text', ['comment' => '让分胜负奖金指数变化'])
            ->addColumn('sp_sfc_var', 'text', ['comment' => '胜分差奖金指数变化'])
            ->addColumn('sp_dxf_var', 'text', ['comment' => '大小分奖金指数变化'])
            ->addColumn('create_at', 'datetime', ['comment' => '创建时间', 'null' => true])
            ->addColumn('update_at', 'datetime', ['comment' => '更改时间', 'null' => true])
            ->addIndex('match_num')
            ->save();
    }
}
