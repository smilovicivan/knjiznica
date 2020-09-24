<?php

include_once "../../config.php";
include_once "../../functions.php";

if (isset($_POST['pay'])) {
    $query = $connect->prepare('select * from user where email=:email');
    $query->bindValue('email', $_POST['email']);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_OBJ);
    if ($user->email === $_SESSION['is_logged_in']->email && $user->role === 'admin') {
        header('Location: all-memberships.php?admin');
    } else {
        if (empty($user)) {
            header("Location: all-memberships.php?fail");
        } else {
            $userId = $user->id;
            $payedAt = date('Y-m-d');
            $expires = date('Y-m-d', strtotime($payedAt . ' + 365 days'));
    
            //admin kreira novu članarinu za korisnika
            payMembership($userId,$expires,$connect);
        }
    }
} else {
    $userId = $_SESSION['is_logged_in']->id;
    $payedAt = date('Y-m-d');
    $expires = date('Y-m-d', strtotime($payedAt . ' + 365 days'));

    //kreira se nova članarina
    payMembership($userId,$expires,$connect);

}
