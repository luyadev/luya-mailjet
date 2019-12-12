<?php

use yii\db\Migration;

class m191205_185401_mailjet_base_table extends Migration
{
    public function up()
    {
        $this->createTable('{{%mailjet_template}}', [
            'id' => $this->primaryKey(),
            'slug' => $this->string(150)->unique(),
            'mjml' => $this->text(),
            'html' => $this->text(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
        ]);

        $this->createTable('{{%mailjet_template_variable}}', [
            'id' => $this->primaryKey(),
            'template_id' => $this->integer()->notNull(),
            'key' => $this->string(),
            'value' => $this->text(),
        ]);
    }

    public function down()
    {
        $this->dropTable('{{%mailjet_template}}');
        $this->dropTable('{{%mailjet_template_variable}}');
    }
}