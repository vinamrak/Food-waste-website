<?php
session_start();

if(!isset($_SESSION['rusername'])){
    header('location:login.php');
}
?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-+0n0xVW2eSR5OomGNYDnhzAbDsOXxcvSN1TPprVMTNDbiYZCxYbOOl7+AMvyTG2x" crossorigin="anonymous">
    <!--FONT FAMILY-->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans&display=swap" rel="stylesheet">
    <!--EXT CSS-->
    <link rel="stylesheet" href="css/styles.css">

    <title>Share food, Save lives!</title>
    <style>
        html{
            scroll-behaviour:smooth!important;
        }
        .reg-input{
            width: 50vw!important;
            padding: 0.25em;
        }
        @media screen and (min-width: 700px) and (max-width: 1200px){
            .reg-input{
                width: 30vw!important;
            }
        }
         @media screen and (min-width: 1200px){
            .reg-input{
                width: 24vw!important;
            }
        }
        label{
            padding-bottom: 6px;
            color: #c2ab92;
        }
        h3{
            margin-bottom: 1em;
        }
    </style>
</head>

<body style="background-color: #222222;">

<?php

include 'dbcon.php';

if(isset($_POST['add'])){
    $type = $_POST['food-type'];
    $name = $_POST['food-name'];
    $quan = $_POST['food-quantity'];
    $wt = $_POST['food-weight'];
    $cook = strftime('%Y-%m-%d %H:%M:%S', strtotime(mysqli_real_escape_string($conn, $_POST['food-cook-time'])));
    $exp = strftime('%Y-%m-%d %H:%M:%S', strtotime(mysqli_real_escape_string($conn, $_POST['food-exp-time'])));
    $startp = strftime('%H:%M:%S', strtotime(mysqli_real_escape_string($conn, $_POST['food-pick-start-time'])));
    $endp = strftime('%H:%M:%S', strtotime(mysqli_real_escape_string($conn, $_POST['food-pick-end-time'])));
    $people = $_POST['food-people'];
    $descp = $_POST['food-desc'];
    $status = 0;
    $rid = $_SESSION['rid'];

    $query = "INSERT INTO food_item(ftype, fname, fquantity, fweight, fcooktime, fexptime, fspick, fepick, fpeople, fdescp, fstatus, rid) VALUES ('$type','$name','$quan','$wt','$cook','$exp','$startp','$endp','$people','$descp','$status','$rid')";
    $que = mysqli_query($conn, $query);
   
    
    if($que){
        echo "<script>alert('Inserted Successfully')</script>";
        $cityque = "SELECT * FROM rest_details WHERE rid = '$rid'";
        $cityexe = mysqli_query($conn, $cityque);
        $assoc = mysqli_fetch_assoc($cityexe);
        $restcity = $assoc['rcity'];

        $ngosque = "SELECT * FROM ngo_details WHERE city LIKE '$restcity'";
        $ngosexe = mysqli_query($conn, $ngosque);
        while($row = mysqli_fetch_assoc($ngosexe)){
            $email = $row['email'];
            $subject = "New Food Item Addition";
            $body = "<html><body><h3>Hello, A new food item has been added by a Restaurant in your city.</h3><p>Login to your website account if you wish to pick up the item!</p><p>FEW DETAILS OF THE ITEM :-<br>Name: $name<br>Type: $type<br>Weight: $wt<br>Approx no of people that can be fed: $people</p></body></html>";
            $headers  = 'MIME-Version: 1.0' . "\r\n";
            $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
            $headers .= "From: sharefood012@gmail.com";
            mail($email, $subject, $body, $headers);
        }
    }
    else{
        echo "<script>alert('Unable to insert');</script>";
    }
}

?>

<div class="container-fluid overflow-hidden">
    <div class="row vh-100 overflow-auto">
        <!--include side nav-->
        <?php include 'components/rest_nav.php'; ?>

        <div class="col d-flex flex-column h-100">
            <main class="row">
                <div class="col py-4" style="color: white;">

                <div class="row welc">
                    <div class="col">WELCOME RESTAURANT <?php echo $_SESSION['rusername']; ?> !</div>
                </div>

                    <center>
                    <form method="POST" action="<?php echo htmlentities($_SERVER['PHP_SELF']); ?>">
                    <h3>Enter the details of your item :</h3>

                    <label for="food-type">Type:</label><br>
                    <select class="reg-input" id="foodtype" name="food-type" required>
                    <option value="Dessert">Dessert</option>
                    <option value="Main Course">Main Course</option>
                    <option value="Beverage">Beverage</option>
                    <option value="Snacks">Snacks</option>
                    </select><br><br>

                    <label for="food-name">Name:</label><br>
                    <input class="reg-input" type="text" id="foodname" name="food-name" pattern="[A-Za-z0-9 ]{1,30}" title="max 30 letters" required><br><br>

                    <label for="food-quantity">Quantity (in pcs):</label><br>
                    <input class="reg-input" type="number" id="foodquan" name="food-quantity" pattern="[1-9]{1}[0-9]{1,3}" title="max 4 digits" required><br><br>

                    <label for="food-weight">Total Weight (in kg):</label><br>
                    <input type="number" id="foodwt" name="food-weight" pattern="[1-9]{1}[0-9]{1,3}" title="max 4 digits" required><br><br>

                    <label for="food-cook-time">Cooked Time:</label><br>
                    <input class="reg-input" type="datetime-local" id="foodcooktime" name="food-cook-time" required><br><br>

                    <label for="food-exp-time">Approx Expiration Time:</label><br>
                    <input class="reg-input" type="datetime-local" id="foodexptime" name="food-exp-time" required><br><br>

                    <label for="food-pick-start-time">Feasible Pickup Time:</label><br>
                    <input type="time" id="foodpickstarttime" name="food-pick-start-time" required> TO <input type="time" id="foodpickendtime" name="food-pick-end-time" required><br><br>

                    <label for="food-people">Approx no. of people that can be fed:</label><br>
                    <input class="reg-input" type="number" id="foodpeople" name="food-people" pattern="[1-9]{1}[0-9]{1,2}" title="max 3 digits" required><br><br>

                    <label for="food-desc">Description (like ingredients, allergin info etc)</label><br>
                    <textarea type="text" rows="4" class="reg-input" id="fooddesc" name="food-desc" required></textarea><br><br>

                    <button class="btn btn-secondary" type="submit" name="add">SUBMIT</button><br> 
                    </form>
                    </center>
                </div>
            </main>
            <footer class="row bg-dark text-white text-center p-3 mt-auto">
            <div class="col"> Developed by <u>Vinamra Khoria</u> & <u>Devansh Baid</u> & <u>Amit Krishna</u> </div>
            </footer>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/js/bootstrap.bundle.min.js" integrity="sha384-gtEjrD/SeCtmISkJkNUaaKMoLD0//ElJ19smozuHV6z3Iehds+3Ulb9Bn9Plx0x4" crossorigin="anonymous"></script>
</body>
</html>