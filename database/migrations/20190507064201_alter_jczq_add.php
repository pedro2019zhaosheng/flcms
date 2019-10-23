<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterJczqAdd extends Migrator
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
        $this->table('jczq_match')
            ->addColumn('nm_id', 'string', ['default' => '', 'comment' => '奖金指数的唯一ID'])
            ->addColumn('issue', 'string', ['default' => '', 'comment' => '期号'])
            ->save();
    }
}
