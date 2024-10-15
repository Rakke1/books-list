<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Book */
/* @var $modelForm app\models\BookForm */
/* @var $authors \app\models\Author[]|array|\yii\db\ActiveRecord[] */

$this->title = 'Добавить книгу';
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelForm' => $modelForm,
        'authors' => $authors,
    ]) ?>

</div>
