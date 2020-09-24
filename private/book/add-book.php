<?php

    include_once "../../config.php";
    //ako nije logiran i ako nije admin ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
        header("Location: " . $path . "index.php");
    }

    if (isset($_POST['addBook'])) {
        $errorMsg = [];
        $successMsg = [];
         //uklanja sva prazna polja
        $name = trim($_POST['name']);
        $author = trim($_POST['author']);
        $amount = $_POST['amount'];
        $zanr = $_POST['zanr'];
        $valid = true;

        // Provjerava jeli koje polje prazno ako je prikazuje poruku, ako nije ubacuje u bazu
        if ($name === '' || $author === '') {
            $valid = false;
            $errorMsg[] = 'Obavezan unos svih polja';
        }
        //Količina knjiga mora biti veća od 0
        if ($amount <= 0) {
            $valid = false;
            $errorMsg[] = 'Količina mora biti veća od 0';
        }

        //za svaku knjigu mora se upload-at slika
        if (empty($_FILES['image']['name'])) {
            $valid = false;
            $errorMsg[] = 'Odaberite sliku';
        }

        //provjere za file: jeli dozovoljenog tipa,dozvoljene velićine...
        if ($_FILES['image']['size'] > 0) {
            $targetDir = '../../assets/uploads/';
            $fileType = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $imageName = strtolower(preg_replace('/\s+/', '', $name) .
                preg_replace('/\s+/', '', $author) . '.' . $fileType);
            $targetFile = $targetDir . $imageName;
            $allowedFileTypes=["jpg", "jpeg"];

            if (!in_array($fileType, $allowedFileTypes)) {
                $errorMsg[] = 'Samo jpg i jpeg formati su dozvoljeni';
                $valid = false;
            }

            if ($_FILES['image']['size'] > 2097152) {
                $errorMsg[] = 'Image size too big';
                $valid = false;
            }

            if ($valid) {
                move_uploaded_file($_FILES['image']['tmp_name'], $targetFile);
            }

        }
        //ako sve provjere prođu ubacuje knjigu u bazu
        if ($valid) {
            $query = $connect->prepare(
                    'insert into book (author, name, amount, image, zanr) 
                              values (:author,:name,:amount,:image,:zanr)');
            $query->bindValue(':author', $author);
            $query->bindValue(':name', $name);
            $query->bindValue(':amount', $amount);
            $query->bindValue(':image', $imageName);
            $query->bindValue(':zanr', $zanr);
            $query->execute();

            $successMsg[] = "Uspješno ste se dodali novu knjigu";
        }
    }

    $query = $connect->prepare('select * from zanr');
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

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

    <title>Dodaj knjigu</title>
</head>
<body>
<?php include_once '../../templates/navbar.php';?>
<?php if (!empty($successMsg)): ?>
    <?php foreach ($successMsg as $s): ?>
        <div>
            <p class="alert alert-success text-center"><?php echo $s; ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
<?php if (!empty($errorMsg)): ?>
    <?php foreach ($errorMsg as $e): ?>
        <div>
            <p class="alert alert-warning text-center"><?php echo $e; ?></p>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<div id="login">
    <div class="container">
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <form id="login-form" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">
                        <h3 class="text-center text-info">Dodaj knjigu</h3>
                        <div class="form-group">
                            <label for="name" class="text-info">Naziv:</label><br>
                            <input type="text" name="name" id="name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="author" class="text-info">Autor:</label><br>
                            <input type="text" name="author" id="author" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="amount" class="text-info">Količina:</label><br>
                            <input type="number" name="amount" id="amount" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="image" class="text-info">Naslovna slika:</label><br>
                            <input type="file" name="image" id="image" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="zanr" class="text-info">Žanr:</label><br>
                            <select name="zanr" id="zanr" class="custom-select">
                                <option value="">--Odaberite--</option>
                                <?php foreach ($results as $result): ?>
                                <option value="<?php echo $result->id ?>"><?php echo htmlspecialchars($result->name) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" name="addBook" class="btn btn-info btn-md" value="Dodaj knjigu">
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
