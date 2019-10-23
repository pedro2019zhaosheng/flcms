<?php

use think\migration\Migrator;
use think\migration\db\Column;

class DelAlert extends Migrator
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
        $table = $this->table('jclq_base');
        $table->removeColumn('issue')->save();
        $table = $this->table('jclq_match');
        $table->removeColumn('nm_id')->removeColumn('issue')->save();
        $table = $this->table('jczq_base');
        $table->removeColumn('issue')->save();
        $table = $this->table('jczq_match');
        $table->removeColumn('issue')->removeColumn('nm_id')->save();
    }
}
