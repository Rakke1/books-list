<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;

/**
 *
 * @property-read string $uploadPath
 */
class BookForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $cover_image_file;

    public function rules(): array
    {
        return [
            [['cover_image_file'], 'file', 'extensions' => 'jpg, jpeg, png', 'maxSize' => 1024 * 1024 * 2],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'cover_image_file' => 'Фото главной страницы',
        ];
    }

    /**
     * @throws \yii\base\Exception
     */
    public function uploadImage(UploadedFile $image, $currentImage = null): false|string
    {
        if (!is_null($currentImage))
            $this->deleteCurrentImage($currentImage);
        $this->cover_image_file = $image;
        if($this->validate())
            return $this->saveImage();
        return false;
    }

    private function getUploadPath(): false|string
    {
        return \Yii::getAlias('@webroot/uploads/books/');
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function generateFileName(): string
    {
        do {
            $name = \Yii::$app->security->generateRandomString();
            $file = strtolower($name .'.'. $this->cover_image_file->extension);
        } while (file_exists($file));
        return $file;
    }

    public function deleteCurrentImage($currentImage): void
    {
        if ($currentImage && $this->fileExists($currentImage)) {
            unlink($this->getUploadPath() . $currentImage);
        }
    }

    /**
     * @param $currentFile
     * @return bool
     */
    public function fileExists($currentFile): bool
    {
        $file = $currentFile ? $this->getUploadPath() . $currentFile : null;
        return file_exists($file);
    }

    /**
     * @return string
     * @throws \yii\base\Exception
     */
    public function saveImage(): string
    {
        $filename = $this->generateFilename();
        $this->cover_image_file->saveAs($this->getUploadPath() . $filename);
        return $filename;
    }
}