<!doctype html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Домашнее задание № 3</title>
</head>
<body>
<h1>Домашнее задание №3. Адиль Габдуллин.</h1>
<?php
require 'functions.php';
$i = 0;
while (++$i <= 4) {
    echo PHP_EOL, "<h2>Задание #$i</h2>", PHP_EOL;
    ("task$i")();
}
?>
</body>
</html>