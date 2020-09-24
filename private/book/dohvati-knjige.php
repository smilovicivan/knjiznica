<?php

    include_once "../../config.php";

    $zanr = $_POST['id'];

    $query = $connect->prepare('select * from book where zanr=:zanr order by name asc');
    $query->bindValue('zanr', $zanr);
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);

    echo json_encode($result);