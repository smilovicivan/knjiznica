<?php
include_once "../../config.php";

$id = $_POST['id'];
$query = $connect->prepare('select * from book where id=:id');
$query->bindValue('id', $id);
$query->execute();
$book = $query->fetch(PDO::FETCH_ASSOC);
//za svaku knjigu mora se upload-at slika
if (empty($_FILES['image']['name'])) {
    $valid = false;
    echo  'Odaberite sliku';
}

//provjere za file: jeli dozovoljenog tipa,dozvoljene velićine...
if ($_FILES['image']['size'] > 0) {
    $valid = true;
    $targetDir = '../../assets/uploads/';
    $fileType = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
    $imageName = strtolower(preg_replace('/\s+/', '', $book['name']) .
        preg_replace('/\s+/', '', $book['author']) . '.' . $fileType);
    $targetFile = $targetDir . $imageName;
    $allowedFileTypes=["jpg", "jpeg"];

    if (!in_array($fileType, $allowedFileTypes)) {
        $errorMsg[] = 'Samo jpg i jpeg formati su dozvoljeni';
        $valid = false;
    }

    if ($_FILES['image']['size'] > 2097152) {
        $errorMsg[] = 'Slika je prevelika';
        $valid = false;
    }

    if ($valid) {
        move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
        $bravo = 'bravo';
        echo json_encode($bravo);
        $query = $connect->prepare('update book set image=:image where id=:id');
        $query->bindValue('image', $imageName);
        $query->bindValue('id', $id);
        $query->execute();
    }

}


