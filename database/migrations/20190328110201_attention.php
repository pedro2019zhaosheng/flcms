<?php

use think\migration\Migrator;
use think\migration\db\Column;

class Attention extends Migrator
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
        $table = $this->table('attention', array('engine' => 'InnoDB', 'comment' => '会员关注表'));
        $table->addColumn('member_id', 'integer', array('comment' => '会员ID'))
            ->addColumn('member_attention_id', 'integer', array('comment' => '被关注会员的ID'))
            ->addColumn('create_at', 'datetime', array('comment' => '创建时间'))
            ->create();
    }
}
