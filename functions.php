<?php

function borrowBook($userId, $bookId, $connect)
{
     try {
          $connect->beginTransaction();

          //u tablicu borrow ubacuje id korisnika i kjnige
          $query = $connect->prepare('insert into borrow (user, book) values(:user,:book)');
          $query->bindValue(':user', $userId);
          $query->bindValue(':book', $bookId);
          $query->execute();

          //iz baze podataka dohvača trenutan broj knjiga koje su dostupne
          $query = $connect->prepare('select amount from book where id=:id');
          $query->bindValue(':id', $bookId);
          $query->execute();
          $bookAmount = $query->fetchColumn();

          //umanjuje trenutno dostupan broj knjiga za 1
          $afterAmount = $bookAmount - 1;
          $query = $connect->prepare('update book set amount=:afterAmount where id=:id');
          $query->bindValue(':afterAmount', $afterAmount);
          $query->bindValue(':id', $bookId);
          $query->execute();

          $connect->commit();

          header('Location: books.php?success');
     } catch (PDOException $e) {
          $connect->rollBack();
      }
    
}

function payMembership($userId, $expires, $connect)
{
    $query = $connect->prepare('insert into membership (user,expires) values(:user,:expires)');
    $query->bindValue(':user', $userId);
    $query->bindValue(':expires', $expires);
    if ($query->execute()) {
        header('Location: all-memberships.php?success');
    } else {
        header('Location: all-memberships.php?fail');
    }
}

function isVerifyed($user, $connect)
{
    $query = $connect->prepare('select verifyed from user where id=:userId');
    $query->bindValue('userId', $user);
    $query->execute();
    $verifyed = $query->fetchColumn();
    if (!$verifyed) {
        return false; 
    } else {
        return true;
    }
}
