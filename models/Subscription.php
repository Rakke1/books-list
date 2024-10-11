<?php

namespace app\models;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "subscriptions".
 *
 * @property int $id
 * @property string $email
 * @property int|null $phone
 * @property int $author_id
 * @property-read ActiveQuery $author
 * @property string $created_at
 */
class Subscription extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return 'subscriptions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['email', 'author_id'], 'required'],
            ['email', 'email'],
            ['phone', 'integer'],
            [['author_id'], 'integer'],
            [['email'], 'string', 'max' => 255],
            [['phone'], 'integer', 'max' => 999999999999999999],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'phone' => 'Номер телефона',
            'author_id' => 'Author ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Связь с таблицей authors.
     * @return ActiveQuery
     */
    public function getAuthor(): ActiveQuery
    {
        return $this->hasOne(Author::class, ['id' => 'author_id']);
    }
}
