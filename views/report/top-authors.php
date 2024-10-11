<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $topAuthors array */
/* @var $year int */

$this->title = "ТОП 10 авторов за год $year";
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="report-index">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php if (!empty($topAuthors)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Автор</th>
                    <th>Количество книг</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topAuthors as $author): ?>
                    <tr>
                        <td><?= Html::encode($author['fio']) ?></td>
                        <td><?= Html::encode($author['book_count']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Нет авторов, выпустивших книги в этом году.</p>
    <?php endif; ?>
</div>

