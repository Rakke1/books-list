<?php

namespace app\models;

use \Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception;

/**
 *
 * @property array $authorsList
 * @property \yii\db\ActiveQuery|array $authors
 */
class Book extends ActiveRecord
{
    public $cover_image_file;
    public $authors;

    public static function tableName(): string
    {
        return 'books';
    }

    public function rules(): array
    {
        return [
            [['title', 'published_year', 'isbn'], 'required'],
            [['description'], 'string'],
            [['published_year'], 'integer', 'min' => 1, 'max' => date('Y')],
            [['isbn'], 'string', 'max' => 13],
            [['cover_image'], 'string', 'max' => 255],
            [['isbn'], 'unique'],
            [['cover_image_file'], 'file',
//                'extensions' => 'jpg, jpeg, png',
//                'maxSize' => 1024 * 1024 * 2
            ],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'title' => 'Название',
            'description' => 'Описание',
            'published_year' => 'Год выпуска',
            'isbn' => 'ISBN',
            'cover_image' => 'Фото главной страницы',
            'cover_image_file' => 'Загрузить фото',
        ];
    }

    public function uploadCoverImage(): bool
    {
        if ($this->cover_image_file) {
            $fileName = Yii::$app->security->generateRandomString() . '.' . $this->cover_image_file->extension;
            $filePath = Yii::getAlias('@webroot/uploads/') . $fileName;

            if ($this->cover_image_file->saveAs($filePath)) {
                if ($this->cover_image) {
                    $oldFile = Yii::getAlias('@webroot/uploads/') . $this->cover_image;
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
                $this->cover_image = $fileName;
                return true;
            }
        }
        $this->cover_image_file = null;

        return false;
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            if ($this->cover_image) {
                $file = Yii::getAlias('@webroot/uploads/') . $this->cover_image;
                if (file_exists($file)) {
                    unlink($file);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAuthors(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Author::class, ['id' => 'author_id'])
            ->viaTable('book_author', ['book_id' => 'id']);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAuthorsList(): array
    {
        return $this->getAuthors()->select('id')->column();
    }

    /**
     * @throws Exception
     */
    public function saveAuthors($authors): void
    {
        \Yii::$app->db->createCommand()->delete('book_author', ['book_id' => $this->id])->execute();

        if (is_array($authors)) {
            foreach ($authors as $authorId) {
                /** @noinspection MissedFieldInspection */
                \Yii::$app->db->createCommand()->insert('book_author', [
                    'book_id' => $this->id,
                    'author_id' => $authorId,
                ])->execute();
            }
        }
    }
}
