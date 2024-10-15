<?php

namespace app\controllers;

use app\models\BookSearch;
use app\models\BookForm;
use Yii;
use app\models\Book;
use app\models\Author;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class BookController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex(): string
    {
        $searchModel = new BookSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new Book();
        $modelForm = new BookForm();
        $authors = Author::find()->select(['fio', 'id'])->indexBy('id')->column();

        if ($model->load(Yii::$app->request->post())) {
            if ($cover_image_file = UploadedFile::getInstance($modelForm, 'cover_image_file')) {
                $model->cover_image = $modelForm->uploadImage($cover_image_file);
            }

            if ($model->save()) {
                $model->saveAuthors(Yii::$app->request->post('Book')['authors']);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        return $this->render('create', [
            'model' => $model,
            'modelForm' => $modelForm,
            'authors' => $authors,
        ]);
    }

    /**
     * @throws NotFoundHttpException|InvalidConfigException|\yii\base\Exception
     */
    public function actionUpdate($id): Response|string
    {
        $modelForm = new BookForm();
        $model = $this->findModel($id);
        $authors = Author::find()->select(['fio', 'id'])->indexBy('id')->column();
        $model->authors = $model->getAuthorsList();

        if ($model->load(Yii::$app->request->post())) {
            if ($modelForm->load(Yii::$app->request->post()) && $modelForm->validate()) {
                if ($cover_image_file = UploadedFile::getInstance($modelForm, 'cover_image_file')) {
                    $model->cover_image = $modelForm->uploadImage($cover_image_file, $model->cover_image);
                }
            }

            if ($model->save()) {
                $model->saveAuthors(Yii::$app->request->post('Book')['authors']);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'modelForm' => $modelForm,
            'authors' => $authors,
        ]);
    }

    /**
     * @throws \Throwable
     * @throws StaleObjectException
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?Book
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая книга не найдена.');
    }
}
