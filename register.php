<?php
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

include_once "config.php";
require_once "vendor/autoload.php";

if (isset($_POST["register"])) { //provjerava dali je forma submitana

    //uklanja sva prazna polja
    $firstName = trim($_POST["firstname"]);
    $lastName = trim($_POST["lastname"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $valid = true;

    $email_activation_key = md5($email . $firstName . $lastName);

    // Provjerava jeli koje polje prazno ako je prikazuje poruku, ako nije ubacuje u bazu
    if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
        $valid = false;
        $message = 'Obavezan unos svih polja';
    }
    //prvi registrirani korisnik ce dobiti role admin, svi ostali dobrit ce role user
    //admin korisniku može promijeniti role u admin ako želi i obrnuto
    if ($valid) {
        try {
            $query = $connect->prepare("insert into user (firstname,lastname,email,pass,role,email_activation_key)
            values (:firstname,:lastname,:email,:pass,:role,:email_activation_key)");
            $query->bindValue(":firstname", $firstName);
            $query->bindValue(":lastname", $lastName);
            $query->bindValue(":email", $email);
            $query->bindValue(":pass", password_hash($password, PASSWORD_DEFAULT));
            $query->bindValue(":role", "admin");
            $query->bindValue(":email_activation_key", $email_activation_key);
            $query->execute();
            $lastID = $connect->lastInsertId();
        } catch (\PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                header("Location: register.php?duplicate-email");
                exit;
            }
        }

        $query = $connect->prepare("select count(id) from user");
        $query->execute();
        $idCount = $query->fetchColumn();

        if ($idCount > 1) {
            $query = $connect->prepare("update user set role=:role where id=:id");
            $query->bindValue("role", "user");
            $query->bindValue("id", $lastID);
            $query->execute();
        }

        $link = 'http://' . $_SERVER['SERVER_NAME'] . '/smilovic/activation.php?key=' . $email_activation_key;

        // Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->SMTPDebug = 0; // Enable verbose debug output
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP(); // Set mailer to use SMTP
            $mail->Host = 'smtp.gmail.com'; // Specify main and backup SMTP servers
            $mail->SMTPAuth = true; // Enable SMTP authentication
            $mail->Username = ''; // SMTP username param
            $mail->Password = ''; // SMTP password param
            $mail->SMTPSecure = 'tls'; // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587; // TCP port to connect to
            $mail->SMTPOptions = array(
                'ssl' => array(
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ),
            );

            //Recipients param
            $mail->setFrom('ivansmilovic17@gmail.com', 'Mailer');
            $mail->addAddress($email); // Add a recipient

            // Content
            $mail->isHTML(true); // Set email format to HTML
            $mail->Subject = 'Verify Your Email Address';
            $mail->Body = "
            <h3>Akrivirajte vaš korisnički račun</h3>
            <p>Da biste aktivirali vaš račun pritisnite <a href=\"$link\">$link</a></p>
            ";

            $mail->send();
            header("Location: register.php?message-sent");
        } catch (Exception $e) {
            header("Location: register.php?message-failed");
        }
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">

    <title>Registracija</title>
</head>
<body>
    <?php include_once 'templates/navbar.php';?>

    <?php if (isset($message)): ?>
    <div>
        <p class="alert alert-warning text-center"><?php echo $message; ?></p>
    </div>
    <?php endif;?>
    <div id="login">
        <?php if (isset($_GET['duplicate-email'])): ?>
            <p class="alert alert-danger text-center">Email koji ste unjeli već je u uporabi.</p>
        <?php elseif (isset($_GET['message-sent'])): ?>
            <p class="alert alert-success text-center">Link sa aktivacijskim kodom vam je poslan na mail.</p>
        <?php elseif (isset($_GET['message-failed'])): ?>
            <p class="alert alert-danger text-center">Poruka se ne može poslati.</p>
        <?php endif;?>
        <div class="container">
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                            <h3 class="text-center text-info">Registracija</h3>
                            <div class="form-group">
                                <label for="firstname" class="text-info">Ime:</label><br>
                                <input type="text" name="firstname" id="firstname" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="lastname" class="text-info">Prezime:</label><br>
                                <input type="text" name="lastname" id="lastname" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="email" class="text-info">Email:</label><br>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Lozinka:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="register" class="btn btn-info btn-md" value="Registriraj se">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>