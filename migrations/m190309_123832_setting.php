<?php

use yii\db\Migration;

/**
 * Class m190309_123832_setting
 */
class m190309_123832_setting extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%setting}}', 'id','INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
        $this->insert('{{%setting}}', ['key' => 'ojName', 'value' => '江南']);
        $this->insert('{{%setting}}', ['key' => 'schoolName', 'value' => '江南大学']);
        $this->insert('{{%setting}}', ['key' => 'scoreboardFrozenTime', 'value' => '7200']);
        $this->insert('{{%setting}}', ['key' => 'isShareCode', 'value' => '1']);
        $this->insert('{{%setting}}', ['key' => 'oiMode', 'value' => '0']);
        $this->insert('{{%setting}}', ['key' => 'isUserReg', 'value' => '1']);
        $this->insert('{{%setting}}', ['key' => 'isDiscuss', 'value' => '1']);
        $this->insert('{{%setting}}', ['key' => 'isDefGroup', 'value' => '3']);
        $this->insert('{{%setting}}', ['key' => 'ojEditor', 'value' => 'app\widgets\kindeditor\KindEditor']);        
        $this->insert('{{%setting}}', ['key' => 'isChangeNickName', 'value' => '2']);
        $this->insert('{{%setting}}', ['key' => 'isGroupJoin', 'value' => '0']);
        $this->insert('{{%setting}}', ['key' => 'isGroupReset', 'value' => '0']);        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['key' => 'ojName']);
        $this->delete('{{%setting}}', ['key' => 'schoolName']);
        $this->delete('{{%setting}}', ['key' => 'scoreboardFrozenTime']);
        $this->delete('{{%setting}}', ['key' => 'isShareCode']);
        $this->delete('{{%setting}}', ['key' => 'oiMode']);
        $this->delete('{{%setting}}', ['key' => 'isUserReg']);
        $this->delete('{{%setting}}', ['key' => 'isDiscuss']);
        $this->delete('{{%setting}}', ['key' => 'isDefGroup']);
        $this->delete('{{%setting}}', ['key' => 'ojEditor']);    
        $this->delete('{{%setting}}', ['key' => 'isChangeNickName']);
        $this->delete('{{%setting}}', ['key' => 'isGroupJoin']);
        $this->delete('{{%setting}}', ['key' => 'isGroupReset']);        
        $this->dropColumn('{{%setting}}', 'id');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190309_123832_setting cannot be reverted.\n";

        return false;
    }
    */
}
