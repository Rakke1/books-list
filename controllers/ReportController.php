<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Author;

class ReportController extends Controller
{
    public function actionTopAuthors($year = null): string
    {
        if ($year === null) {
            $year = date('Y');
        }

        $topAuthors = Author::getTopAuthorsByYear($year);

        return $this->render('top-authors', [
            'topAuthors' => $topAuthors,
            'year' => $year,
        ]);
    }
}
