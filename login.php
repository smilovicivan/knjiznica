<?php
include_once "config.php";

if (isset($_SESSION['is_logged_in'])) {
    header("Location: index.php");
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

    <title>Prijava</title>
</head>
<body>
    <?php include_once 'templates/navbar.php';?>

    <div id="login">
        <div class="container">
            <?php if (isset($_GET['message']) && $_GET['message'] == 1): ?>
                <p class="alert alert-danger text-center">Neispravna lozinka</p>
            <?php elseif (isset($_GET['message']) && $_GET['message'] == 2): ?>
                <p class="alert alert-danger text-center">Netočan email</p>
            <?php elseif (isset($_GET['verifyed'])):?>
                <p class="alert alert-success text-center">Uspiješno ste aktivirali vaš korisnički račun. Prijavite se.</p>
            <?php elseif (isset($_GET['fail-verification'])):?>
                <p class="alert alert-danger text-center">Neispravan aktivacijski kod ili je mail već aktiviran.</p>
            <?php endif; ?>
            <div id="login-row" class="row justify-content-center align-items-center">
                <div id="login-column" class="col-md-6">
                    <div id="login-box" class="col-md-12">
                        <form id="login-form" class="form" action="authorizathion.php" method="post">
                            <h3 class="text-center text-info">Prijava</h3>
                            <div class="form-group">
                                <label for="username" class="text-info">Email:</label><br>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="password" class="text-info">Lozinka:</label><br>
                                <input type="password" name="password" id="password" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="login" class="btn btn-info btn-md" value="Prijavi se">
                                <a href="<?php echo $path ?>register.php" class="float-right btn btn-info">registriraj se</a>
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