<?php

class m140817_101237_fix extends CDbMigration
{
	public function safeUp()
	{
        $this->addColumn('{{gs}}', 'stats_items', 'tinyint(1) unsigned NOT NULL DEFAULT \'1\'');
        $this->addColumn('{{gs}}', 'stats_items_list', 'text');
        $this->update('{{config}}', array('field_type' => 'dropDownList'), 'param = :param', array('param' => 'waytopay.sms.allow'));

        $name = 'Платежная система: Waytopay';

        $groupId = $this->getDbConnection()->createCommand("SELECT id FROM {{config_group}} WHERE name = :name LIMIT 1")
            ->bindParam('name', $name, PDO::PARAM_STR)
            ->queryRow();

        $this->insert('{{config}}', array(
            'param' => 'waytopay.sms.prefix',
            'value' => 'ghtweb',
            'default' => 'ghtweb',
            'label' => 'SMS, префикс',
            'group_id' => $groupId['id'],
            'order' => 6,
            'method' => NULL,
            'field_type' => 'textField',
            'created_at' => date('Y-m-d H:i:s'),
        ));

        $this->update('{{config}}', array('label' => 'Публичный ключ'), 'param = :param', array('param' => 'unitpay.public_key'));
        $this->update('{{config}}', array('order' => 4), 'param = :param', array('param' => 'unitpay.project_id'));

        $this->alterColumn('{{transactions}}', 'sum', 'decimal(10,2) unsigned NOT NULL COMMENT \'Сумма\'');
        $this->addColumn('{{bonuses}}', 'date_end', 'datetime DEFAULT NULL');
	}

	public function safeDown()
	{
		$this->dropColumn('{{gs}}', 'stats_items');
		$this->dropColumn('{{gs}}', 'stats_items_list');
        $this->update('{{config}}', array('field_type' => 'textField'), 'param = :param', array('param' => 'waytopay.sms.allow'));
        $this->delete('{{config}}', 'param = :param', array('param' => 'waytopay.sms.prefix'));

        $this->alterColumn('{{transactions}}', 'sum', 'decimal(10,0) unsigned NOT NULL COMMENT \'Сумма\'');
        $this->dropColumn('{{bonuses}}', 'date_end');
	}
}