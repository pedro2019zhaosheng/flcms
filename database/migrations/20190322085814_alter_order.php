<?php

use think\migration\Migrator;
use think\migration\db\Column;

class AlterOrder extends Migrator
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
        $table->removeColumn('is_follow_order')
            ->changeColumn('bet_type', 'string', ['comment' => '玩法类型, 1单关 2过关(串关)', 'default' => 2])
            ->changeColumn('chuan', 'string', ['comment' => '串关信息, ["3@1", "4@2"] 三串一, 四串二', 'default' => ''])
            ->removeColumn('play_type')
            ->changeColumn('bet_content', 'text', ['comment' => '投注项'])
            ->changeColumn('status', 'boolean', ['limit' => 2, 'comment' => '状态0：待出票 1：已出票 2：待开奖 3：未中奖 4：已中奖 5: 已派奖'])
            ->removeColumn('order_type')
            ->changeColumn('pay_type', 'boolean', ['limit' => 2, 'comment' => '购买方式1：自购 2：跟单 3: 推单'])
            ->update();

        $table = $this->table('order_content');
        $table->removeColumn('bet')
            ->changeColumn('content', 'text', ['comment' => '投注内容'])
            ->changeColumn('status', 'boolean', ['comment' => '中奖状态, 0 待开奖 1 已中奖 2未中奖', 'default' => 0])
            ->update();
    }
}
