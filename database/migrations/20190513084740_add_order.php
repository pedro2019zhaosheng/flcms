<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AddOrder extends Migrator
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
        $table = $this->table('order');
        $table->changeColumn('is_yh', 'boolean', ['default' => 0, 'comment' => '是否优化(竞彩) 0:不优化 1:优化  中奖后停止追号(数字彩) 0:停止 1:继续'])
            ->changeColumn('beishu', 'integer', ['default' => 0, 'comment' => '倍数(竞彩)  期数(数字彩)'])
            ->changeColumn('zhu', 'integer', ['default' => 0, 'comment' => '总注数'])
            ->changeColumn('beizhu', 'string', ['default' => '', 'comment' => '备注'])
            ->changeColumn('username', 'biginteger', ['default' => 0, 'comment' => '用户账号'])
            ->changeColumn('bonus', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '竞彩奖金'])
            ->changeColumn('bounty', 'decimal', ['precision' => 18, 'scale' => 2, 'default' => "0.00", 'comment' => '竞彩嘉奖彩金'])
            ->changeColumn('status', 'boolean', ['default' => 0, 'comment' => '状态, 通用状态(0：待出票 1：已出票)  竞彩状态(2：待开奖 3：未中奖 4：已中奖 5: 已派奖)'])
            ->update();
    }
}
