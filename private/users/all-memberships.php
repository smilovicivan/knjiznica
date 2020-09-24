<?php
    include_once "../../config.php";
    include_once "../../functions.php";

    //ako nije logiran ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"])) {
        header("Location: " . $path . "index.php");
    }

    //izvlaći iz baze podataka članarine svih korisnika i samo admin ih može vidjeti
    $query = $connect->prepare('
                    select a.id, concat(b.firstname, " ",b.lastname) as user, a.payedAt, expires 
                    from membership a
                    inner join user b
                    on a.user=b.id
                    order by user desc, user asc'
                    );
    $query->execute();
    $allMemberships = $query->fetchAll(PDO::FETCH_OBJ);
    
    //izvlaći iz baze podataka članarine logiranog korisnika i samo on ih može vidjeti
    $userId = $_SESSION['is_logged_in']->id;
    $query = $connect->prepare(
                    'select a.id, concat(b.firstname, " ",b.lastname) as user, a.payedAt, expires 
                    from membership a
                    inner join user b
                    on a.user=b.id
                    where user=:userId
                    order by a.payedAt desc'   
                );
    $query->bindValue(':userId', $userId);
    $query->execute();
    $userMemberships = $query->fetchAll(PDO::FETCH_OBJ);

    //prebrojava aktivne članarine, ako korisnik nema aktivnih članarina ne može posuđivati knjige
    $userId = $_SESSION['is_logged_in']->id;
    $query = $connect->prepare('select count(id) from membership where status=1 and user=:userId');
    $query->bindValue(':userId', $userId);
    $query->execute();
    $count = $query->fetchColumn();


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

    <title>Posudbe</title>
</head>
<body>
<?php include_once '../../templates/navbar.php';?>
<div class="container-fluid">
    <div class="row">
        <?php include_once '../../templates/sidebar.php' ?>
        <div class="col-lg-10 font-color">
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success text-center" role="alert">
                    Uspiješno ste platili godišnju članarinu
                </div>
            <?php elseif (isset($_GET['fail'])): ?>
                <div class="alert alert-warning text-center" role="alert">
                    Nešto je pošlo po zlu probajte ponovo
                </div>
            <?php elseif (isset($_GET['admin'])): ?>
                <div class="alert alert-warning text-center" role="alert">
                    Ulogirani ste kao admin i ne možete platiti članarinu za sebe
                </div>
            <?php endif; ?>
            <?php if ($count == 0): ?>
                <?php if ($_SESSION["is_logged_in"]->role === 'user' && isVerifyed($_SESSION['is_logged_in']->id, $connect)): ?>
                    <a class="btn btn-primary btn-lg btn-block" href="membership.php">Plati članarinu</a>
                <?php elseif ($_SESSION["is_logged_in"]->role === 'admin'): ?>
                    <a class="btn btn-primary btn-lg btn-block" href="pay-membership-for.php">Plati članarinu</a>
                <?php endif; ?>
            <?php endif; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th><b>Korisnik</b></th>
                        <th><b>Plaćeno</b></th>
                        <th><b>Vrijedi do</b></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                        <?php foreach ($allMemberships as $am): ?>
                            <tr>
                                <td><?php echo $am->user ?></td>
                                <td><?php echo date('d-m-Y', strtotime($am->payedAt)) ?></td>
                                <td><?php echo date('d-m-Y', strtotime($am->expires)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'user'): ?>
                        <?php foreach ($userMemberships as $um): ?>
                            <tr>
                                <td><?php echo $um->user ?></td>
                                <td><?php echo date('d-m-Y', strtotime($um->payedAt)) ?></td>
                                <td><?php echo date('d-m-Y', strtotime($um->expires)) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
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
