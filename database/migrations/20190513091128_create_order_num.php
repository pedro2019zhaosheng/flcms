<?php

use think\migration\Migrator;
use think\migration\db\Column;

class CreateOrderNum extends Migrator
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
        $table = $this->table('order_num', ['engine' => 'InnoDB', 'comment' => '数字彩追号详情表']);
        $table->addColumn('number', 'string', ['default' => '', 'comment' => '期号'])
            ->addColumn('order_id', 'biginteger', ['default' => 0, 'comment' => '订单ID'])
            ->addColumn('multiple', 'integer', ['default' => 1, 'comment' => '倍数'])
            ->addColumn('amount', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => '0.00', 'comment' => '单期金额'])
            ->addColumn('bonus', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '单期奖金'])
            ->addColumn('bounty', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '单期嘉奖彩金'])
            ->addColumn('status', 'boolean', ['default' => 1, 'comment' => '1：待开奖 2：未中奖 3：已中奖 4: 已派奖'])
            ->addColumn('create_time', 'datetime', ['null' => true, 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => true, 'comment' => '修改时间'])
            ->addIndex('order_id')
            ->addIndex('number')
            ->addIndex('status')
            ->save();
    }
}
