<?php

namespace app\controllers;

use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\Response;
use yii\data\Pagination;
use yii\web\NotFoundHttpException;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\SignupForm;
use app\models\Contest;
use app\models\Discuss;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionConstruction()
    {
        return $this->render('construction');
    }

    public function actionNews($id)
    {
        $model = Discuss::find()->where(['id' => $id, 'status' => Discuss::STATUS_PUBLIC, 'entity' => Discuss::ENTITY_NEWS])->one();

        if ($model === null) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }

        return $this->render('news', [
            'model' => $model
        ]);
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $contests = Yii::$app->db->createCommand('
            SELECT id, title FROM {{%contest}}
            WHERE status = :status AND type != :type AND end_time >= :time
            ORDER BY start_time DESC LIMIT 3
        ', [':status' => Contest::STATUS_VISIBLE, ':type' => Contest::TYPE_HOMEWORK, ':time' => date('Y:m:d h:i:s', time())])->queryAll();

        $query = (new Query())->select('id, title, content, created_at')
            ->from('{{%discuss}}')
            ->where(['entity' => Discuss::ENTITY_NEWS, 'status' => Discuss::STATUS_PUBLIC])
            ->orderBy('id DESC');

        $pages = new Pagination(['totalCount' => $query->count()]);
        $news = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();

        return $this->render('index', [
            'contests' => $contests,
            'pages' => $pages,
            'news' => $news
        ]);
    }

    public function actionPrint()
    {
        return $this->render('print');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionSignup()
    {
        $model = new SignupForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
