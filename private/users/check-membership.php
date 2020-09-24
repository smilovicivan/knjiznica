<?php

include_once "../../config.php";

$userId = $_SESSION['is_logged_in']->id;
$current = date('Y-m-d');

//svakih x minuta ajax salje request i provjerava za logiranog korisnika ima li aktivnih članarina
//provjerava se i jeli određena članarina istekla i ako je mijenja joj se status u neaktivna
$query = $connect->prepare('select id, expires from membership where user=:userId');
$query->bindValue(':userId', $userId);
$query->execute();
$result = $query->fetchAll(PDO::FETCH_OBJ);
foreach ($result as $r) {
    if ($r->expires < $current) {
        $query = $connect->prepare('update membership set status=0 where id=:id');
        $query->bindValue(':id',$r->id);
        $query->execute();
    }
}

$query = $connect->prepare('select count(id) from membership where status=1 and user=:userId');
$query->bindValue(':userId', $userId);
$query->execute();
$count = $query->fetchColumn();
echo json_encode($count) ;


