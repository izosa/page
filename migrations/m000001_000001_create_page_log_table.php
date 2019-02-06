<?

use yii\db\Migration;

/**
 * Handles the creation of table `page_log`.
 */
class m000001_000001_create_page_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%page_log}}', [
            'id' => $this->primaryKey(),
            'created_at' => $this->dateTime(),
            'url' => $this->text(),
            'file' => $this->text(),
            'useragent' => $this->text(),
            'proxy' => $this->string(),
            'status' => $this->integer(),
            'tag' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('page_log');
    }
}
