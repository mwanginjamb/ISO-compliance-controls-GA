<?php

use yii\db\Migration;
use yii\db\Query;
use yii\helpers\HtmlPurifier;

/**
 * Sanitizes content stored before input filtering was added to the models.
 *
 * Rich-text (TinyMCE) columns are passed through HtmlPurifier, plain-text
 * columns have their tags stripped. Not reversible.
 */
class m261101_100300_sanitize_stored_content extends Migration
{
    const COLUMNS = [
        '{{%requirements}}' => [
            'rich' => ['description', 'evidence_path', 'gaps', 'actions_required'],
            'plain' => ['assignee'],
        ],
        '{{%sub_clause}}' => [
            'rich' => ['sub_clause'],
            'plain' => ['number'],
        ],
        '{{%clause}}' => [
            'rich' => ['description'],
            'plain' => ['title'],
        ],
        '{{%standards}}' => [
            'rich' => [],
            'plain' => ['standard'],
        ],
    ];

    public function safeUp()
    {
        foreach (self::COLUMNS as $table => $columns) {
            $select = array_merge(['id'], $columns['rich'], $columns['plain']);
            foreach ((new Query())->select($select)->from($table)->each(100, $this->db) as $row) {
                $changes = [];
                foreach ($columns['rich'] as $column) {
                    if ($row[$column] !== null && $row[$column] !== '') {
                        $clean = HtmlPurifier::process($row[$column]);
                        if ($clean !== $row[$column]) {
                            $changes[$column] = $clean;
                        }
                    }
                }
                foreach ($columns['plain'] as $column) {
                    if ($row[$column] !== null && $row[$column] !== '') {
                        $clean = trim(strip_tags($row[$column]));
                        if ($clean !== $row[$column]) {
                            $changes[$column] = $clean;
                        }
                    }
                }
                if ($changes) {
                    $this->update($table, $changes, ['id' => $row['id']]);
                }
            }
        }
    }

    public function safeDown()
    {
        echo "m261101_100300_sanitize_stored_content cannot be reverted.\n";
        return true;
    }
}
