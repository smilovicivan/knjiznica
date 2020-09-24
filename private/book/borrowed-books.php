<?php
    include_once "../../config.php";
    include_once "../../functions.php";

    //ako nije logiran ne može pristupiti stranici
    if (!isset($_SESSION["is_logged_in"])) {
        header("Location: " . $path . "index.php");
    }

    //izvlači iz baze posudbe svih korisnika, samo admin može vidjeti sve
    $query = $connect->prepare(
                    'select a.id, concat(b.firstname, " ", b.lastname) as user, b.email, a.book, c.name, 
                    a.returned, a.borrowedAt, a.returnedAt from borrow a 
                    inner join user b 
                    on a.user=b.id 
                    inner join book c 
                    on a.book=c.id
                    order by a.borrowedAt desc'
    );
    $query->execute();
    $all = $query->fetchAll(PDO::FETCH_OBJ);

    //izvlači iz baze podataka posudbe logiranog korisnika
    $userId = $_SESSION['is_logged_in']->id;
    $query = $connect->prepare(
                    'select a.id, concat(b.firstname, " ", b.lastname) as user, b.email, c.name, a.borrowedAt, a.returnedAt from borrow a 
                    inner join user b 
                    on a.user=b.id 
                    inner join book c 
                    on a.book=c.id 
                    where user=:id
                    order by a.borrowedAt desc'
    );
    $query->bindValue(':id', $userId);
    $query->execute();
    $userBorrow = $query->fetchAll(PDO::FETCH_OBJ);

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
        <div class="col-lg-10">
            <?php if (isset($_GET['zakasnina'])): ?>
                <div class="alert alert-warning" role="alert">
                    Potrebno platiti zakasninu u iznosu od <?php echo $_GET['zakasnina'] ?> kn
                </div>
            <?php endif; ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Korisnik</th>
                        <th>Email</th>
                        <th>Knjiga</th>
                        <th>Lokacija</th>
                        <th>Datum posudbe</th>
                        <th>Vraćeno</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($_SESSION["is_logged_in"]) && $_SESSION["is_logged_in"]->role === 'admin'): ?>
                        <?php foreach ($all as $a): ?>
                        <tr>
                            <td><?php echo $a->user ?></td>
                            <td><?php echo $a->email ?></td>
                            <td><?php echo $a->name ?></td>
                            <td><?php echo date('d-m-Y', strtotime($a->borrowedAt)) ?></td>
                            <?php if ($a->returnedAt !== null): ?>
                                <td><?php echo date('d-m-Y', strtotime($a->returnedAt)) ?></td>
                            <?php else: ?>
                                <td>Nije vraćena</td>
                            <?php endif; ?>
                            <?php if ($a->returned): ?>
                                <td>Knjiga vraćena</td>
                            <?php else: ?>
                                <td><a class="btn btn-primary" href="return-book.php?book=<?php echo $a->book ?>&id=<?php echo $a->id ?>">Vrati knjigu</a></td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <?php foreach ($userBorrow as $borrow): ?>
                            <tr>
                                <td><?php echo $borrow->user ?></td>
                                <td><?php echo $borrow->email ?></td>
                                <td><?php echo $borrow->name ?></td>
                                <td><?php echo date('d-m-Y', strtotime($borrow->borrowedAt)) ?></td>
                                <?php if ($borrow->returnedAt !== null): ?>
                                    <td><?php echo date('d-m-Y', strtotime($borrow->returnedAt)) ?></td>
                                <?php else: ?>
                                    <td>Nije vraćena</td>
                                <?php endif; ?>
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
