<?php

if (!isset($_POST["email"])){ // provjerava dali je email poslan, ako nije odma izlazi van
    exit;
}

include_once "config.php"; //config file s podatcima za spajanje na bazu podataka

if($_POST["email"]===""){
    header("location: login.php?message=2"); //ako je email prazan vraća nazad na login
    exit;
}

$query = $connect->prepare("select * from user where email=:email");
$query->execute(array("email"=>$_POST["email"]));

$user = $query->fetch(PDO::FETCH_OBJ);

if($user !=null && $user->pass==password_verify($_POST["password"],$user->pass)){
    //pusti dalje i ukloni lozinku iz sessiona
    $user->pass="";
    //stavlja sve potatke o logiranom korisnuku u session
    $_SESSION["is_logged_in"]=$user;
    $query = $connect->prepare('update user set active=1 where id=:id');
    $query->bindValue(':id', $_SESSION['is_logged_in']->id);
    $query->execute();
    header("location: index.php");
}else {
    header("location: login.php?message=1");
}