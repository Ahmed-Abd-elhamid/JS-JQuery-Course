<?php
require_once "pdo.php";
session_start();

if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM Profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$profile_id = $row['profile_id'];
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$e = htmlentities($row['email']);
$h = htmlentities($row['headline']);
$s = htmlentities($row['summary']);
?>

<!DOCTYPE html>
<html>
<head>
<?php require_once "bootstrap.php"; ?>
<title>Ahmed Abdelhamid - 7530b57b</title>
</head>
<body>
<div class="container">
<h1>Profile information</h1>
<?php     
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
?>
<p><b><u>First Name:</u></b> <span><?= $fn ?></span></p>
<p><b><u>Last Name:</u></b> <span><?= $ln ?></span></p>
<p><b><u>Email:</u></b> <span><?= $e ?></span></p>
<p><b><u>Headine:</u></b> <br><span><?= $h ?></span></p>
<p><b><u>Summary:</u></b> <br><span><?= $s ?></span></p>
<?php 
    $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xz");
    $stmt->execute(array(":xz" => $_GET['profile_id']));
    $len = 0;
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)){
        $len ++;
        ?>
            <p><b><u>Year:</u></b> <br><span><?= htmlentities($rw['year'])?></span></p>
            <p><b><u>Description:</u></b> <br><span><?= htmlentities($rw['description'])?></span></p>  
        <?php
    }
?>
<a href="index.php">Done</a>
</div>
</body>
</html>