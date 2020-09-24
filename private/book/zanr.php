<?php

    include_once "../../config.php";
    //ako nije logiran i ako nije admin ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
        header("Location: " . $path . "index.php");
    }

    //izvlaći sve korisnike iz baze podataka
    $query = $connect->prepare("select * from zanr");
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

    <title>Naslovna</title>
</head>
<body>
    <?php include_once '../../templates/navbar.php';?>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h1>Žanrovi</h1>
                <a class="btn btn-primary btn-lg btn-block" href="<?php echo $path ?>private/book/dodaj-zanr.php">Dodaj zanr</a>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Naziv</th>
                        <td>Akcija</td>
                    </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $result): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($result->name) ?></td>
                            <td>
                                <a class="btn btn-info" href="edit-zanr.php?id=<?php echo $result->id ?>">Izmjeni</a>
                                <a class="btn btn-warning" href="delete-zanr.php?id=<?php echo $result->id ?>">Obriši</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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
