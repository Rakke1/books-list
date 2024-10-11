<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Author */

$this->title = $model->fio;
$this->params['breadcrumbs'][] = ['label' => 'Список авторов', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!Yii::$app->user->isGuest): ?>
        <p>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif; ?>

    <?php if (Yii::$app->user->isGuest): ?>
        <div class="subscribe-form">
            <h3>Подпишитесь на новые книги автора</h3>
            <?= Html::beginForm(['author/subscribe', 'author_id' => $model->id], 'post') ?>
            <?= Html::input('email', 'email', '', ['class' => 'form-control', 'placeholder' => 'Введите ваш email', 'required' => true]) ?>
            <?= Html::input('tel', 'phone', '+7', [
                'class' => 'form-control',
                'placeholder' => 'Введите ваш номер телефона',
                'required' => false,
                'id' => 'phone-input',
            ]) ?>
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-success']) ?>
            <?= Html::endForm() ?>
        </div>
    <?php endif; ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fio',
        ],
    ]) ?>

</div>
