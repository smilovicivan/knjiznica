<?php

include_once "../../config.php";

include_once "../../functions.php";

if (isset($_POST['borrow'])) {
    $query = $connect->prepare('select * from user where email=:email');
    $query->bindValue('email', $_POST['email']);
    $query->execute();
    $user = $query->fetch(PDO::FETCH_OBJ);
    if ($user->email === $_SESSION['is_logged_in']->email && $user->role === 'admin') {
        header('Location: books.php?admin');
    } else {
        if (empty($user)) {
            header('Location: books.php?wrong-mail');
        } else {
            $userId = $user->id;
            $bookId = $_POST['book'];
            $query = $connect->prepare('select count(id) from borrow where user=:userId and returned=0');
            $query->bindValue(':userId', $userId);
            $query->execute();
            $maxBooks = $query->fetchColumn();
            if ($maxBooks < 5) {
                borrowBook($userId,$bookId,$connect);
            } else {
                header('Location: books.php?max');
            }
            
            
        }
    }
} else {
    $bookId = $_GET['id'];
    $userId = $_SESSION['is_logged_in']->id;

    borrowBook($userId,$bookId,$connect);
}
