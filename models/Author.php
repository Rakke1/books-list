<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 *
 * @property-read ActiveQuery $books
 */
class Author extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'authors';
    }

    public function rules(): array
    {
        return [
            [['fio'], 'required'],
            [['fio'], 'string', 'max' => 255],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'fio' => 'ФИО',
        ];
    }

    /**
     * @throws InvalidConfigException
     */
    public function getBooks(): ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('book_author', ['author_id' => 'id']);
    }

    public static function getTopAuthorsByYear($year, $limit = 10)
    {
        return (new Query())
            ->select(['authors.id', 'authors.fio', 'COUNT(books.id) AS book_count'])
            ->from('authors')
            ->leftJoin('book_author', 'book_author.author_id = authors.id')
            ->leftJoin('books', 'books.id = book_author.book_id')
            ->where(['books.published_year' => $year])
            ->groupBy('authors.id')
            ->orderBy(['book_count' => SORT_DESC])
            ->limit($limit)
            ->all();
    }
}
