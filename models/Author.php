<?php

namespace app\models;

use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 *
 * @property-read \yii\db\ActiveQuery $books
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
    public function getBooks(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Book::class, ['id' => 'book_id'])
            ->viaTable('book_author', ['author_id' => 'id']);
    }
}
