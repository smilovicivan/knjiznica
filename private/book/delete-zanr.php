<?php

    include_once "../../config.php";
    //ako nije logiran i ako nije admin ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
        header("Location: " . $path . "index.php");
    }

    //brise određenog korisnika
    $query = $connect->prepare("delete from zanr where id=:id");
    $query->bindValue(":id", $_GET["id"]);
    $query->execute();
    $message = 'Žanr obrisan';

    header("Location: zanr.php");