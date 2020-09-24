<?php

include_once "../../config.php";

$bookId = $_GET['book'];
$borrowId = $_GET['id'];

//izvlači broj trenutno dostupnih knjiga
$query = $connect->prepare('select amount from book where id=:id');
$query->bindValue(':id', $bookId);
$query->execute();
$bookAmount = (int)$query->fetchColumn();

//uvećava broj dostupnih knjiga za 1
$newAmount = $bookAmount + 1;

//update u bazu s novim brojem trenutno dostupnih knjiga
$query = $connect->prepare('update book set amount=:newAmount where id=:bookId');
$query->bindValue(':newAmount', $newAmount);
$query->bindValue(':bookId', $bookId);
$query->execute();

//izvlači iz baze kada je knjiga posuđena
$query = $connect->prepare('select borrowedAt from borrow where id=:borrowId');
$query->bindValue(':borrowId', $borrowId);
$query->execute();
$borrowAt = $query->fetchColumn();

//datum kada je knjiga posuđena
$borrowAt = date('Y-m-d', strtotime($borrowAt));
//do kada se knjiga treba vratiti
$mustReturnTo = date('Y-m-d', strtotime($borrowAt . ' + 14 days'));

//datum kada je knjiga vraćena
$returnedAt = date('Y-m-d');

$query = $connect->prepare('update borrow set returned=1, returnedAt=:returnedAt where id=:borrowId');
$query->bindValue(':borrowId', $borrowId);
$query->bindValue(':returnedAt', $returnedAt);
$query->execute();
if ($returnedAt > $mustReturnTo) {
    $daysDiff = strtotime($returnedAt) - strtotime($mustReturnTo);
    $days = round($daysDiff / (60 * 60 * 24));
    $overdue = $days * 1;
    header('Location: borrowed-books.php?zakasnina=' . $overdue);
} else {
    header('Location: borrowed-books.php');
}
