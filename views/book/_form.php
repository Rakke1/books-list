<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\file\FileInput;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Book */
/* @var $form yii\widgets\ActiveForm */
/* @var $authors \app\models\Author[]|array|\yii\db\ActiveRecord[] */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'published_year')->textInput(['type' => 'number']) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cover_image_file')->widget(FileInput::class, [
        'options' => ['accept' => 'image/*'],
        'pluginOptions' => [
            'initialPreview' => [
                $model->cover_image ? Html::img("/uploads/{$model->cover_image}", ['style' => 'width:200px;']) : null,
            ],
            'overwriteInitial' => true,
            'showRemove' => false,
            'showUpload' => false,
        ],
    ]) ?>

    <?= $form->field($model, 'authors')->widget(Select2::class, [
        'data' => $authors,  // Переданный массив авторов
        'options' => ['placeholder' => 'Выберите авторов ...', 'multiple' => true],
        'pluginOptions' => [
            'allowClear' => true,
        ],
    ]); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
