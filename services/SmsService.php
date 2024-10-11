<?php

namespace app\services;

use app\models\Subscription;
use Yii;

class SmsService
{
    public static function sendSmsToSubscribers($authorId, $text): array
    {
        $apiKey = env('SMS_API_KEY');
        $sender = env('SMS_SENDER');

        $subscriptions = Subscription::find()->where(['author_id' => $authorId])->all();
        $results = [];

        foreach ($subscriptions as $subscription) {
            $phone = $subscription->phone;

            $url = 'https://smspilot.ru/api.php'
                .'?send='.urlencode($text)
                .'&to='.urlencode($phone)
                .'&from='.$sender
                .'&apikey='.$apiKey
                .'&format=json';

            try {
                $json = @file_get_contents($url);
                $j = json_decode($json);

                if (!isset($j->error)) {
                    $results[] = 'SMS успешно отправлена на номер '.$phone;
                } else {
                    $errorMessage = 'Ошибка при отправке SMS на номер '.$phone.': '.$j->error->description_ru;
                    $results[] = $errorMessage;
                    Yii::error($errorMessage, __METHOD__);
                }
            } catch (\Exception $e) {
                $errorMessage = 'Ошибка при отправке SMS на номер '.$phone.': '.$e->getMessage();
                $results[] = $errorMessage;
                Yii::error($errorMessage, __METHOD__);
            }
        }

        return $results;
    }
}
