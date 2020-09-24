<?php
include_once "../../config.php";

//ako nije logiran i ako nije admin ne može pristupiti stranici
if (!isset($_SESSION["is_logged_in"]) || $_SESSION["is_logged_in"]->role !== 'admin') {
    header("Location: " . $path . "index.php");
}

if (isset($_POST['editBook'])) {
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

    if ($valid) {
        $query = $connect->prepare('update book set author=:author,name=:name,zanr=:zanr,amount=:amount where id=:bookId');
        $query->bindValue(':bookId', $_POST['id']);
        $query->bindValue(':author', $author);
        $query->bindValue(':name', $name);
        $query->bindValue(':amount', $amount);
        $query->bindValue(':zanr', $zanr);
        $query->execute();
        header("Location: books.php");
    }
} else {
    $query = $connect->prepare('select * from book where id=:id');
    $query->bindValue('id', $_GET['id']);
    $query->execute();
    $_POST = $query->fetch(PDO::FETCH_ASSOC);
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

    <title>Popis knjiga</title>
</head>
<body>

<?php include_once '../../templates/navbar.php';?>
    <div class="container">
        <div class="row">
            <div class="col-8 offset-2">
            <div class="alert" role="alert">
                
            </div>
                <div class="card" style="height: 100%; margin-top: 20px;">
                    <img src="../../assets/uploads/<?php echo $_POST['image'] ?>" style="height: 450px;" class="card-img-top" alt="...">
                    <div class="card-body">
                        <form id="uploadImage" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="image" class="text-info">Naslovna slika:</label><br>
                                <input type="file" name="image" id="image" class="form-control">
                            </div>
                            <div class="form-group">
                                <input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>">
                                <input type="submit" name="editBookImg" class="btn btn-info btn-md" value="Promijeni sliku">
                            </div>
                        </form>
                        <hr>
                        <form id="login-form" class="form" action="<?php echo $_SERVER["PHP_SELF"] ?>" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="name" class="text-info">Naziv:</label><br>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo $_POST['name'] ?>" >
                            </div>
                            <div class="form-group">
                                <label for="author" class="text-info">Autor:</label><br>
                                <input type="text" name="author" id="author" class="form-control" value="<?php echo $_POST['author'] ?>" >
                            </div>
                            <div class="form-group">
                                <label for="amount" class="text-info">Količina:</label><br>
                                <input type="number" name="amount" id="amount" class="form-control" value="<?php echo $_POST['amount'] ?>" >
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
                                <input type="hidden" name="id" value="<?php echo $_POST["id"]; ?>">
                                <input type="submit" name="editBook" class="btn btn-info btn-md" value="Promijeni">
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>

<script>
    $(document).ready(function (e) {
        $("#uploadImage").on('submit',function(e) {
            e.preventDefault();
            $.ajax({
                url: "change-book-image.php",
                type: "POST",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData:false,
                success: function(data){
                   $(".alert").append(data); 
                
                }
            })
        })
    })
</script>

</body>
</html>
