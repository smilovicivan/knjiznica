<?php

include_once "config.php";
include_once "functions.php";
//provjerava dali postoji koja aktivna članarina
if (isset($_SESSION['is_logged_in'])) {
    $userId = $_SESSION['is_logged_in']->id;
    $query = $connect->prepare('select count(id) from membership where status=1 and user=:userId');
    $query->bindValue(':userId', $userId);
    $query->execute();
    $count = $query->fetchColumn();
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" 
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/style.css">

    <title>Naslovna</title>
</head>
<body style="background-image: none">
    <?php include_once 'templates/navbar.php';?>
    <div class="container-fluid">

        <div class="row">
            <?php include_once 'templates/sidebar.php'?>
            <div class="col-lg-10">
                <h3 class="text-center">Dobrošli na stranicu knjižnice!!!</h3>
                <p class="text-center">"Svaka knjiga koju ovdje vidiš bila je nekomu najbolji prijatelj." (Carlos Ruiz Zafon)</p>
                <div class="row justify-content-center">
                    <?php foreach ($results as $result): ?>
                        <div class="card col-4 zanr" style="width: 18rem;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($result->name) ?></h5>
                                <a href="#" class="btn btn-primary prikazi-knjige" id="<?php echo $result->id ?>">Popis knjiga</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal" id="exampleModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Naziv - Autor</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
            </div>
            </div>
        </div>
    </div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.min.js" 
    integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" 
    crossorigin="anonymous"></script>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" 
    ="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<script>
    $(document).ready(function(){
        $(".prikazi-knjige").on("click", function (e) {
            e.preventDefault();
            myId = $(this).attr('id');
            $(".modal-body").html("");
            
            $.ajax({
                type: "POST",
                url: "private/book/dohvati-knjige.php",
                data: { id: myId},
                success: function (response) {
                    var jsonData = JSON.parse(response);
                    var knjiga = '';
                    for (var i = 0; i < jsonData.length; i++) {
                        knjiga += '<p>' + jsonData[i].name + ' - ' + jsonData[i].author + '</p>';
                    }
                    if (jsonData.length === 0) {
                        $(".modal-body").append("Trenutno prazna polica");
                    }
                    $(".modal-body").append(knjiga);
                    $("#exampleModal").modal("show");
                }
            });
        })

        $.ajax({
            type: 'POST',
            url: 'private/users/check-membership.php',
            success: function (response) {

            }
        })

        setInterval(function () {
            $.ajax({
            type: 'POST',
            url: 'private/users/check-membership.php',
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
            url: 'private/users/active-users.php',
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
                url: 'private/users/active-users.php',
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