<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

include_once "../../config.php";
require_once "../../vendor/autoload.php";


//izvlači iz baze kada je knjiga posuđena
$query = $connect->prepare('select a.borrowedAt, b.name, c.firstname, c.lastname, c.email from borrow a
                        inner join book b
                        on a.book=b.id 
                        inner join user c
                        on a.user=c.id
                        where returned = :returned');
$query->bindValue(':returned', false);
$query->execute();
$borrows = $query->fetchAll(PDO::FETCH_OBJ);

foreach ($borrows as $borrow) {
    
    //datum kada je knjiga posuđena
    $borrowAt = date('Y-m-d', strtotime($borrow->borrowedAt));
    
    //do kada se knjiga treba vratiti
    $mustReturnTo = date('Y-m-d', strtotime($borrowAt . ' + 14 days'));
    
    //datum kada ce biti poslan mail da obavijesti korisnika
    $sendEmailDate = date('Y-m-d', strtotime($mustReturnTo . ' - 2 days'));
    
    //trenutni datum koji se uspoređuje s datumom kada će mail biti poslan
    $currentDate = date('Y-m-d');
    

    if ($currentDate === $sendEmailDate) {
        
        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0; // Enable verbose debug output
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = ''; // SMTP username
            $mail->Password = ''; // SMTP password
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
            );

            //Recipients
            $mail->setFrom('ivansmilovic17@gmail.com', 'Mailer');
            $mail->addAddress($borrow->email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = $borrow->name;
            $mail->Body = "
            <h3>Obavijest</h3>
            <p>Poštovani, ". $borrow->firstname . " " . $borrow->lastname . ". <br>Za dva dana vam ističe rok za vraćanje knjige "
            . $borrow->name . " kako ne biste morali platiti zakasninu vratite knjigu u zadanom roku. 
            <br>Lijep pozdrav, osoblje knjižnice</p>";

            $mail->send();
            
        } catch (Exception $e) {
            
        }
    }

    
}
