<?php

$app->get(
    '/wpcenter',
    $authenticateUser('user'),
    function () use ($app) {
        $app->render('wpcenter.php');
    }
);

?>