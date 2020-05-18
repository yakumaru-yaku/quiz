<?php

session_start();

function h($s) {
    return htmlspecialchars($s, ENT_QUOTES, "UTF-8");
}
function resetSession() {
    $_SESSION['correct_count'] = 0;
    $_SESSION['num'] = 0;
    $_SESSION['token'] = sha1(uniqid(mt_rand(), true));
}
function redirect() {
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit;
}
$quizList = [
    [
        'q' => 'HTMLのHはなんの略？',
        'a' => ['Hyper', 'Hop', 'Hip', 'Hot']
    ],
    [
        'q' => 'HTMLのTはなんの略？',
        'a' => ['Text', 'Ttttt', 'tq', 'Tggg']
    ],
    [
        'q' => 'HTMLのMはなんの略？',
        'a' => ['Markup', 'm', 'mmm', 'mkmkfgm']
    ],
    [
        'q' => 'HTMLのLはなんの略？',
        'a' => ['Language', 'llll', 'loo', 'lklkl']
    ],
];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_POST['token'] !== $_SESSION['token']) {
        echo "不正な投稿！(1)";
        exit;
    }
    if (isset($_POST['reset']) && $_POST['reset'] === '1') {
        resetSession();
        redirect();
    }
    if (!isset($_POST['qnum']) || $_SESSION['qnum'] !== $_POST['qnum']) {
        echo "不正な投稿！(2)";
        exit;
    }
    if (!isset($quizList[$_POST['qnum']])) {
        echo "不正な投稿です！(3)";
        exit;
    }
    if ($_POST['answer'] === $quizList[$_POST['qnum']]['a'][0]) {
        $_SESSION['correct_count']++;
    }
    $_SESSION['num']++;
    header('Location: http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    exit;
}
if (empty($_SESSION)) {
    resetSession();
}

$_SESSION['token'] = sha1(uniqid(mt_rand(), true));

$qnum = mt_rand(0, count($quizList) - 1);
$quiz = $quizList[$qnum];
$_SESSION['qnum'] = (string)$qnum;
shuffle($quiz['a']);

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>!簡単クaaイズ!!</title>
</head>
<body>
<div style="padding: 7px; background: #eee; border: #ccc;">
<?php echo h($_SESSION['num']); ?><span>問中、</span><?php echo h($_SESSION['correct_count']); ?><span>門正解！</span>
<?php if ($_SESSION['num'] > 0) : ?>
    <span>正答率は<?php echo h(sprintf("%.2f", $_SESSION['correct_count'] / $_SESSION['num'] * 100) ); ?>%です。</span>
<?php endif; ?>
</div>
    <p>Q. <?php echo h($quiz['q']); ?></p>
    <?php foreach ($quiz['a'] as $answer) : ?>
        <form action="" method="post">
            <input type="submit" name="answer" value="<?php echo h($answer); ?>">
            <input type="hidden" name="qnum" value="<?php echo h($_SESSION['qnum']); ?>">
            <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
        </form>
    <?php endforeach; ?>
    <hr>
    <form action="" method="post">
        <input type="submit" value="リセット">
        <input type="hidden" name="reset" value="1">
        <input type="hidden" name="token" value="<?php echo h($_SESSION['token']); ?>">
    </form>
</body>
</html>
