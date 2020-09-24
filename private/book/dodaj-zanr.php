<?php

    include_once "../../config.php";
    //ako nije logiran i ako nije admin ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
        header("Location: " . $path . "index.php");
    }

    if (isset($_POST['addZanr'])) {
        //uklanja sve prazne znakove
        $name = isset($_POST['name']) ? trim($_POST['name']) : '';
        $valid = true;

        if ($name === '') {
            $valid = false;
            $message = 'Obavezan unos naziva';
        }

        //ubacuje u bazu
        if ($valid) {
            $query = $connect->prepare("insert into zanr (name) 
            values (:name)");
            $query->bindValue(":name", $name);
            $query->execute();

            $message = "Uspješno ste se dodali novi žanr";
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

    <title>Dodaj žanr</title>
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
                        <h3 class="text-center text-info">Dodaj žanr</h3>
                        <div class="form-group">
                            <label for="name" class="text-info">Naziv:</label><br>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="addZanr" class="btn btn-info btn-md" value="Dodaj žanr">
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
