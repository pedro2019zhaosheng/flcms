<?php

use think\migration\Migrator;
use think\migration\db\Column;

class MemberToken extends Migrator
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
        $table = $this->table('member_token', array('engine' => 'InnoDB', 'comment' => '用户API接口访问令牌表'));
        $table->addColumn('member_id', 'integer', array('null'=>false, 'comment' => '用户ID'))
            ->addColumn('token', 'char', array('limit' => 40, 'default' => '', 'null' => false, 'comment' => '令牌'))
            ->addColumn('key','string',array('limit'=>40,'default'=>'','null'=>false,'comment'=>'数据加密key'))
            ->addColumn('create_time', 'datetime', array('null' => true, 'comment' => '创建时间'))
            ->addIndex(array('member_id'), array('unique' => true))
            ->addIndex(array('token'), array('unique' => true))
            ->addIndex(array('key'), array('unique' => true))
            ->create();
    }
}
