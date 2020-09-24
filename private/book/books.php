<?php
include_once "../../config.php";
include_once "../../functions.php";
//izvlači sve knjige iz baze
$query = $connect->prepare("select * from book");
$query->execute();
$results = $query->fetchAll(PDO::FETCH_OBJ);

//provjerava dali postoji koja aktivna članarina, ako ne postoji korisnik ne može posuditi knjigu
if (isset($_SESSION['is_logged_in'])) {
    $userId = $_SESSION['is_logged_in']->id;
    $query = $connect->prepare('select count(id) from membership where status=1 and user=:userId');
    $query->bindValue(':userId', $userId);
    $query->execute();
    $count = $query->fetchColumn();

    //check if user borrowed 5 or more books which is limit
    $query = $connect->prepare('select count(id) from borrow where user=:userId and returned=0');
    $query->bindValue(':userId', $userId);
    $query->execute();
    $maxBooks = $query->fetchColumn();

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

    <title>Popis knjiga</title>
</head>
<body>
<?php include_once '../../templates/navbar.php';?>
    <div class="container-fluid">
        <div class="row">
            <?php include_once '../../templates/sidebar.php' ?>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-12">
                        <?php if (isset($_SESSION['is_logged_in']) && $maxBooks >= 5): ?>
                            <div class="alert alert-info text-center" role="alert">
                                Posudili ste maksimalan broj knjiga.
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_GET['success'])): ?>
                            <div class="alert alert-success text-center" role="alert">
                                Uspiješno ste posudili knjigu!
                            </div>
                        <?php elseif (isset($_GET['wrong-mail'])):?>
                            <div class="alert alert-danger text-center" role="alert">
                               Netočan mail ili ne postoji korisnik s unesenim mailom
                            </div>
                        <?php elseif (isset($_GET['admin'])): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                Ulogirani ste kao admin i ne možete posuditi knjigu
                            </div>
                        <?php elseif (isset($_GET['max'])): ?>
                            <div class="alert alert-danger text-center" role="alert">
                                Korisnik je posudio maksimalan broj knjiga
                            </div>
                        <?php endif; ?>
                        <?php if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                            <a class="btn btn-primary btn-lg btn-block" href="add-book.php">Dodaj knjigu</a>
                        <?php endif;?>
                    </div>
                </div>
                <div class="row">
                    <?php foreach ($results as $result): ?>
                        <?php if ($result->amount > 0 && $result->active == true && (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role !== 'admin' || !isset($_SESSION["is_logged_in"]))): ?>
                        <div class="col-3 book">
                            <div class="card" style="height: 100%;">
                                <img src="../../assets/uploads/<?php echo $result->image ?>" style="height: 250px;" class="card-img-top" alt="...">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $result->name ?></h5>
                                    <p class="card-text"><?php echo $result->author ?></p>
                                    <p class="card-text">Dostupno knjiga: <?php echo $result->amount ?></p>
                                    <?php if (isset($_SESSION['is_logged_in']) && $count > 0 && $maxBooks < 5 && isVerifyed($_SESSION['is_logged_in']->id, $connect)): ?>
                                        <a href="borrow-book.php?id=<?php echo $result->id ?>" class="btn btn-primary">Posudi</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php elseif (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                            <div class="col-3 book">
                                <div class="card" style="height: 100%;">
                                    <img src="../../assets/uploads/<?php echo $result->image ?>" style="height: 250px;" class="card-img-top" alt="...">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo $result->name ?></h5>
                                        <p class="card-text"><?php echo $result->author ?></p>
                                        <p class="card-text">Dostupno knjiga: <?php echo $result->amount ?></p>
                                        <?php if (isset($_SESSION['is_logged_in']) && $count > 0 && $maxBooks < 5): ?>
                                            <a href="borrow-book.php?id=<?php echo $result->id ?>" class="btn btn-primary">Posudi</a>
                                        <?php elseif (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                                            <?php if ($result->active == true && $result->amount > 0): ?>
                                                <a href="borrow-for.php?id=<?php echo $result->id ?>" class="btn btn-primary">Posudi</a>
                                            <?php endif; ?>
                                            <a href="edit-book.php?id=<?php echo $result->id ?>" class="btn btn-info">Uredi</a>
                                            <?php if ($result->active == true): ?>
                                                <a href="delete-book.php?id=<?php echo $result->id ?>" class="btn btn-danger">Ukloni</a>
                                            <?php else: ?>
                                                <a href="show-book.php?id=<?php echo $result->id ?>" class="btn btn-secondary">Prikaži</a>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach;?>
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
    $(document).ready(function(){
        $.ajax({
            type: 'POST',
            url: '../users/check-membership.php',
            success: function (response) {
                
            }
        })

        setInterval(function () {
            $.ajax({
            type: 'POST',
            url: '../users/check-membership.php',
                success: function (response) {
                    
                }
            })
        }, 3600000)

        //datum kada se salje mail upozorenja
        $.ajax({
            type: 'POST',
            url: 'private/book/check-return-date.php',
            success: function (response) {
                
            }
        })
    
        $.ajax({
            type: 'POST',
            url: '../users/active-users.php',
            success: function (response) {
                $(".user").remove();
                var jsonData = JSON.parse(response);
                var message = '';
                for (var i = 0; i < jsonData.length; i++) {
                    message += '<li class="list-group-item user">' + jsonData[i].firstname + ' ' + jsonData[i].lastname + '</li>';
                }

                $('.sidebar').append(message);
            }
        });

        setInterval(function () {
            $.ajax({
                type: 'POST',
                url: '../users/active-users.php',
                success: function (response) {
                    $(".user").remove();
                    var jsonData = JSON.parse(response);
                    var message = '';
                    for (var i = 0; i < jsonData.length; i++) {
                        message += '<li class="list-group-item user">' + jsonData[i].firstname + ' ' + jsonData[i].lastname + '</li>';
                    }

                    $('.sidebar').append(message);
                }
            });
        }, 120000)
    });
</script>
</body>
</html>
