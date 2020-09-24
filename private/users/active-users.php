<?php

    include_once "../../config.php";
    
    //svakih x minuta ajax šalje request i provjerava koji su korisnici trenutno aktivni i ispisuje ih u sidebar
    $query = $connect->prepare('select id, firstname, lastname, email, active from user');
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_OBJ);
    $active = [];
    foreach ($result as $r) {
        if ($r->active) {
            $active[] = $r;
        }
    }
   
    echo json_encode($active);
