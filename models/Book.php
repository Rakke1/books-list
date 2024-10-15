<?php

namespace app\models;

use app\services\SmsService;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Query;

/**
 *
 * @property array $authorsList
 * @property ActiveQuery|array $authors
 */
class Book extends ActiveRecord
{
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
            'authors' => 'Авторы',
        ];
    }

    public function getCoverImagePath()
    {
        if ($this->cover_image)
            return $this->getCoverImage($this->cover_image);
        return '';
    }

    private function getCoverImage(string $filename): string
    {
        return '/uploads/books/'  . $filename;
    }

    public function beforeDelete(): bool
    {
        if (parent::beforeDelete()) {
            $this->deleteImage();
            return true;
        }
        return false;
    }

    public function deleteImage(): void
    {
        $form = new BookForm();
        $form->deleteCurrentImage($this->cover_image);
    }

    /**
     * @throws InvalidConfigException
     */
    public function getAuthors(): ActiveQuery
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
