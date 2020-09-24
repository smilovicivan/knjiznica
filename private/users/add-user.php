<?php

    include_once "../../config.php";
    //ako nije logiran i ako nije admin ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
        header("Location: " . $path . "index.php");
    }

    if (isset($_POST['addUser'])) {
        //uklanja sve prazne znakove
        $firstName = trim($_POST["firstname"]);
        $lastName = trim($_POST["lastname"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $valid = true;

        // Provjerava jeli koje polje prazno ako je prikazuje poruku, ako nije ubacuje u bazu
        if ($firstName === '' || $lastName === '' || $email === '' || $password === '') {
            $valid = false;
            $message = 'Obavezan unos svih polja';
        }
        //ubacuje u bazu
        if ($valid) {
            $query = $connect->prepare("insert into user (firstname,lastname,email,pass,role) 
            values (:firstname,:lastname,:email,:pass,:role)");
            $query->bindValue(":firstname", $firstName);
            $query->bindValue(":lastname", $lastName);
            $query->bindValue(":email", $email);
            $query->bindValue(":pass", password_hash($password,PASSWORD_DEFAULT));
            $query->bindValue(":role", "user");
            $query->execute();

            $message = "Uspješno ste se dodali novog korisnika";
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
    <link rel="stylesheet" href="../../assets/css/style.css">

    <title>Dodaj korsinika</title>
</head>
<body>
<?php include_once '../../templates/navbar.php';?>

<?php if (isset($message)): ?>
    <div>
        <p class="alert alert-warning text-center"><?php echo $message; ?></p>
    </div>
<?php endif; ?>
<div id="login">
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post">
                        <h3 class="text-center text-info">Dodaj korisnika</h3>
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
                            <input type="submit" name="addUser" class="btn btn-info btn-md" value="Dodaj korisnika">
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
