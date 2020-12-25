<?php

/* @var $this \yii\web\View */

/* @var $content string */
/* @var $model app\models\Contest */

use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use app\widgets\Alert;
use app\models\Contest;

AppAsset::register($this);

$this->registerJsFile('/js/jquery.countdown.min.js', ['depends' => 'yii\web\JqueryAsset']);
$model = $this->params['model'];
$status = $model->getRunStatus();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title>
        <?= Html::encode($this->title) ?><?php if(Html::encode($this->title) != Yii::$app->setting->get('ojName')) echo " - " . Yii::$app->setting->get('ojName');?>
    </title>
    <?php $this->head() ?>
    <link rel="shortcut icon" href="<?= Yii::getAlias('@web') ?>/favicon.ico">
    <style>
    .progress-bar {
        transition: none !important;
    }
    </style>
</head>

<body style="padding-top: 56px;">

    <div class="progress hidden-print rounded-0 bg-light">
        <div class="progress-bar progress-bar-success" id="contest-progress" role="progressbar" aria-valuenow="60"
            aria-valuemin="0" aria-valuemax="100" style="width: 0%;">
        </div>
    </div>

    <?php $this->beginBody() ?>

    <div>

        <?php
    NavBar::begin([
        'brandLabel' => Yii::$app->setting->get('ojName'),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar navbar-expand-lg navbar-dark bg-dark fixed-top',
        ],
        'innerContainerOptions' => ['class' => 'container-fluid']
    ]);
    $menuItemsLeft = [
        ['label' => Yii::t('app', 'Home'), 'url' => ['/site/index']],
        [
            'label' => Yii::t('app', 'Problems'),
            'url' => ['/problem/index'],
            'active' => Yii::$app->controller->id == 'problem'
        ],
        ['label' => Yii::t('app', 'Status'), 'url' => ['/solution/index']],
        [
            'label' => Yii::t('app', 'Rating'),
            'url' => ['/rating/index'],
            'active' => Yii::$app->controller->id == 'rating'
        ],
        [
            'label' => Yii::t('app', 'Group'),
            'url' => Yii::$app->user->isGuest ? ['/group/index'] : ['/group/my-group'],
            'active' => Yii::$app->controller->id == 'group'
        ],
        [
            'label' => Yii::t('app', 'Contests'),
            'url' => ['/contest/index'],
            'active' => Yii::$app->controller->id == 'contest'
        ],
        [
            'label' => Yii::t('app', 'Wiki'),
            'url' => ['/wiki/index'],
            'active' => Yii::$app->controller->id == 'wiki'
        ]
    ];
    if (Yii::$app->user->isGuest) {
        $menuItemsRight[] = ['label' => Yii::t('app', 'Signup'), 'url' => ['/site/signup']];
        $menuItemsRight[] = ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']];
    } else {
        if (Yii::$app->user->identity->isAdmin()) {
            $menuItemsRight[] = [
                'label' => Yii::t('app', 'Backend'),
                'url' => ['/admin'],
                'active' => Yii::$app->controller->module->id == 'admin'
            ];
        }
        if  (Yii::$app->user->identity->isVip()) {
            $menuItemsRight[] = [
                'label' => Yii::t('app', 'Backend'),
                'url' => ['/admin/problem'],
                'active' => Yii::$app->controller->module->id == 'admin'
            ];
        }
        $menuItemsRight[] =  [
            'label' => Yii::t('app', 'Setting'),
            'url' => ['/user/setting', 'action' => 'profile'],
        ];
        $menuItemsRight[] = [
            'label' => Yii::t('app', 'Logout'),
            'url' => ['/site/logout'],
        ];
    }
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav mr-auto'],
        'items' => $menuItemsLeft,
        'encodeLabels' => false,
        'activateParents' => true
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav'],
        'items' => $menuItemsRight,
        'encodeLabels' => false,
        'activateParents' => true
    ]);
    NavBar::end();
    ?>



        <div class="container-fluid">
            <div class="col-lg-10 offset-lg-1">
                <!-- <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            'itemTemplate' => "<li class=\"breadcrumb-item\">{link}</li>\n",
            'activeItemTemplate' => "<li class=\"breadcrumb-item active\">{link}</li>\n"
        ]) ?> -->


                <br />
                <?= Alert::widget() ?>

                <?php if (!$model->canView()): ?>
                <?= $content ?>
                <?php elseif ($status == $model::STATUS_NOT_START): ?>
                <?php
                    $menuItems = [
                    [
                        'label' => '<span class="glyphicon glyphicon-home"></span> ' . Yii::t('app', 'Information'),
                        'url' => ['contest/view', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark active']
                    ]
                    ];
                echo Nav::widget([
                    'items' => $menuItems,
                    'options' => ['class' => 'nav nav-tabs hidden-print', 'style' => 'margin-bottom: 15px'],
                    'encodeLabels' => false
                ]) ?>
                <div class="card bg-secondary text-white">
                    <div class="card-body">
                        <h3><?= $model->title ?></h3>
                    </div>
                </div>
                <p></p>
                <div class="card">
                    <div class="card-header">
                        距离比赛开始
                    </div>
                    <div class="card-body text-center">
                        <h1 id="countdown"></h1>
                    </div>
                </div>
                <?php if (!empty($model->description)): ?>
                <!-- <div class="contest-desc">
                    <?= Yii::$app->formatter->asMarkdown($model->description) ?>
                </div> -->
                <?php endif; ?>
                <?php else: ?>
                <div class="contest-view">
                    <?php
                $menuItems = [
                    [
                        'label' => '<span class="glyphicon glyphicon-home"></span> ' . Yii::t('app', 'Information'),
                        'url' => ['contest/view', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ],
                    [
                        'label' => '<span class="glyphicon glyphicon-list"></span> ' . Yii::t('app', 'Problem'),
                        'url' => ['contest/problem', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ],
                    [
                        'label' => '<span class="glyphicon glyphicon-signal"></span> ' . Yii::t('app' , 'Status'),
                        'url' => ['contest/status', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ],
                    [
                        'label' => '<span class="glyphicon glyphicon-glass"></span> ' . Yii::t('app', 'Standing'),
                        'url' => ['contest/standing', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ],
                    [
                        'label' => '<span class="glyphicon glyphicon-comment"></span> ' . Yii::t('app', 'Clarification'),
                        'url' => ['contest/clarify', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ],
                ];
                if ($model->scenario == $model::SCENARIO_OFFLINE && $model->getRunStatus() == $model::STATUS_RUNNING) {
                    $menuItems[] = [
                        'label' => '<span class="glyphicon glyphicon-print"></span> 打印服务',
                        'url' => ['/contest/print', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ];
                }
                if ($model->isContestEnd()) {
                    $menuItems[] = [
                        'label' => '<span class="glyphicon glyphicon-info-sign"></span> ' . Yii::t('app', 'Editorial'),
                        'url' => ['contest/editorial', 'id' => $model->id],
                        'linkOptions' => ['class' => 'text-dark']
                    ];
                }
                echo Nav::widget([
                    'items' => $menuItems,
                    'options' => ['class' => 'nav nav-tabs hidden-print', 'style' => 'margin-bottom: 15px'],
                    'encodeLabels' => false
                ]) ?>
                    <?= $content ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <br />
    <?php $this->endBody() ?>
    <script>
    var client_time = new Date();
    var diff = new Date("<?= date("Y/m/d H:i:s")?>").getTime() - client_time.getTime();
    var start_time = new Date("<?= $model->start_time ?>");
    var end_time = new Date("<?= $model->end_time ?>");
    $("#countdown").countdown(start_time.getTime() - diff, function(event) {
        $(this).html(event.strftime('%D:%H:%M:%S'));
        if ($(this).html() == "00:00:00:00") location.reload();
    });

    function clock() {
        var h, m, s, n, y, mon, d;
        var x = new Date(new Date().getTime() + diff);
        y = x.getYear() + 1900;
        if (y > 3000) y -= 1900;
        mon = x.getMonth() + 1;
        d = x.getDate();
        h = x.getHours();
        m = x.getMinutes();
        s = x.getSeconds();

        n = y + "-" + (mon >= 10 ? mon : "0" + mon) + "-" + (d >= 10 ? d : "0" + d) + " " + (h >= 10 ? h : "0" + h) +
            ":" + (m >= 10 ? m : "0" + m) + ":" + (s >= 10 ?
                s : "0" + s);
        if (document.getElementById('nowdate')) {
            document.getElementById('nowdate').innerHTML = n;
        }

        var now_time = new Date(n);
        if (now_time < end_time) {
            var rate = (now_time - start_time) / (end_time - start_time) * 100;
            document.getElementById('contest-progress').style.width = rate + "%";
        } else {
            document.getElementById('contest-progress').style.width = "100%";
        }
        setTimeout("clock()", 1000);
    }
    clock();

    $(document).ready(function() {
        // 连接服务端
        var socket = io(document.location.protocol + '//' + document.domain + ':2120');
        var uid = '<?= Yii::$app->user->isGuest ? session_id() : Yii::$app->user->id ?>';
        // 连接后登录
        socket.on('connect', function() {
            socket.emit('login', uid);
        });
        // 后端推送来消息时
        socket.on('msg', function(msg) {
            alert(msg);
        });

        $('.pre p').each(function(i, block) { // use <pre><p>
            hljs.highlightBlock(block);
        });
    });
    </script>
</body>

</html>
<?php $this->endPage() ?>