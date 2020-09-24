<?php

include_once "config.php";
$query = $connect->prepare('update user set active=0 where id=:id');
$query->bindValue(':id', $_SESSION['is_logged_in']->id);
$query->execute();
unset($_SESSION["is_logged_in"]);
header("location: index.php");