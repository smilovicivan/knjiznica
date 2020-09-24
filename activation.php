<?php

include_once "config.php";

if (!empty($_GET['key']) && isset($_GET['key'])) {
    $email_activation_key = $_GET['key'];

    $query = $connect->prepare('select * from user where email_activation_key=:key');
    $query->bindValue('key', $email_activation_key);
    $query->execute();
    $result = $query->fetch(PDO::FETCH_OBJ);
    if (empty($result)) {
        header("Location: login.php?fail-verification");
    } else {
        $query = $connect->prepare('update user set verifyed=1 where id=:id');
        $query->bindValue('id',$result->id);
        $query->execute();
        header("Location: login.php?verifyed");
    }

} else {
    header("Location: login.php?fail-verification");
}