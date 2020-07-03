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
<title>Ahmed Abdelhamid - 68b7007a</title>
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
<p>First Name: <span><?= $fn ?></span></p>
<p>Last Name: <span><?= $ln ?></span></p>
<p>Email: <span><?= $e ?></span></p>
<p>Headine: <br><span><?= $h ?></span></p>
<p>Summary: <br><span><?= $s ?></span></p>
<a href="index.php">Done</a>
</div>
</body>
</html>