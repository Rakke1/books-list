<?php

namespace app\services;

use Yii;
use app\models\Subscription;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

class SubscriptionService
{
    /**
     *
     * @param string $email
     * @param int|null $phone
     * @param int $author_id
     * @return bool
     * @throws ServerErrorHttpException|Exception
     */
    public static function subscribeGuest(string $email, int|null $phone, int $author_id): bool
    {
        if (Subscription::find()->where(['email' => $email, 'author_id' => $author_id])->exists()) {
            Yii::$app->session->setFlash('error', 'Вы уже подписаны на этого автора.');
            return false;
        }

        $subscription = new Subscription();
        $subscription->email = $email;
        $subscription->phone = $phone;
        $subscription->author_id = $author_id;

        if (!$subscription->validate()) {
            $errors = $subscription->getErrors();
            $errorMessages = '';

            foreach ($errors as $attribute => $messages) {
                foreach ($messages as $message) {
                    $errorMessages .= "$message ";
                }
            }

            Yii::$app->session->setFlash('error', trim($errorMessages));
            return false;
        }

        if ($subscription->save()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на новые книги автора.');
            return true;
        } else {
            throw new ServerErrorHttpException('Не удалось сохранить подписку. Пожалуйста, попробуйте позже.');
        }
    }
}
