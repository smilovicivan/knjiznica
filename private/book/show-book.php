<?php

include_once "../../config.php";

//ako nije logiran i ako nije admin ne može pristupiti stranici
if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
    header("Location: " . $path . "index.php");
}

//knjiga se brise nego joj se samo uklanja vidljivost te korisnici nece moci posuđivati
$query = $connect->prepare('update book set active=1 where id=:id');
$query->bindValue('id', $_GET['id']);
$query->execute();
header("Location: books.php");