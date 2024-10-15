<?php

namespace app\models;

use app\services\SmsService;
use \Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

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

    /**
     * @throws \yii\base\Exception
     */
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
        $currentAuthors = (new Query())
            ->select('author_id')
            ->from('book_author')
            ->where(['book_id' => $this->id])
            ->column();
        $newAuthors = array_filter((array)$authors);
        $authorsToAdd = array_diff($newAuthors, $currentAuthors);
        $authorsToRemove = array_diff($currentAuthors, $newAuthors);

        if (!empty($authorsToRemove)) {
            \Yii::$app->db->createCommand()->delete('book_author', [
                'book_id' => $this->id,
                'author_id' => $authorsToRemove,
            ])->execute();
        }

        if (!empty($authorsToAdd)) {
            foreach ($authorsToAdd as $authorId) {
                /** @noinspection MissedFieldInspection */
                \Yii::$app->db->createCommand()->insert('book_author', [
                    'book_id' => $this->id,
                    'author_id' => $authorId,
                ])->execute();
                SmsService::sendSmsToSubscribers($authorId, 'У нас новые книги от ваших любимых авторов!');
            }
        }
    }
}
