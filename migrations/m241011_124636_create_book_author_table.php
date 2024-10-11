<?php

use yii\db\Migration;

/**
 * Class m241010_080200_create_book_author_table
 */
class m241011_124636_create_book_author_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('book_author', [
            'book_id' => $this->integer()->notNull(),
            'author_id' => $this->integer()->notNull(),
            'PRIMARY KEY(book_id, author_id)',
        ]);

        $this->addForeignKey(
            'fk-book-author-book_id',
            'book_author',
            'book_id',
            'books',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-book-author-author_id',
            'book_author',
            'author_id',
            'authors',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-book-author-book_id', 'book_author');
        $this->dropForeignKey('fk-book-author-author_id', 'book_author');
        $this->dropTable('book_author');
    }
}
