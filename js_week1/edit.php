<?php
   session_start();
   if (isset($_SESSION['name'])){
       require_once "pdo.php";
   }else{
       die ("ACCESS DENIED");
   }

if ( isset($_POST['cancel'])){
   header("Location: index.php");
   return;
}

if ( isset($_POST['save'])){
   if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

       if ( empty($_POST['first_name']) || empty($_POST['last_name']) || empty($_POST['email']) || empty($_POST['headline']) || empty($_POST['summary']) ){
           $_SESSION['error'] = 'All fields are required';
           header("Location: edit.php?profile_id=".$_GET['profile_id']);
           return;
       }

       if ( strpos($_POST['email'],'@') === false ) {
           $_SESSION['error'] = 'Email address must contain @';
           header("Location: edit.php?profile_id=".$_GET['profile_id']);
           return;
       }


       try{
        $sql = "UPDATE Profile SET first_name = :ft_nm, last_name = :lt_nm, email = :mail, headline = :hd, summary = :sum WHERE profile_id = :profile_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ':ft_nm' => $_POST['first_name'],
            ':lt_nm' => $_POST['last_name'],
            ':mail' => $_POST['email'],
            ':hd' => $_POST['headline'],
            ':sum' => $_POST['summary'],
            ':profile_id' => $_GET['profile_id']));
        $_SESSION['success'] = 'Record updated';
        header( 'Location: index.php' ) ;
        return;
       }catch (Exception $ex){
           
       }

   }else{
       $_SESSION['error'] = 'All fields are required';
       header("Location: edit.php?profile_id=".$_GET['profile_id']);
       return;  
   }
}

// Guardian: Make sure that profile_id is present
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
<?php 
    echo '<h1>Adding Profile for '.$_SESSION['name'].'</h1>';
    // Flash pattern
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }   
    if ( isset($_SESSION['error']) ) {
        echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
        unset($_SESSION['error']);
    }
?>
<form method="post">
<p>First Name: <input type="text" name="first_name" size="60" value="<?= $fn ?>"></p>
<p>Last Name: <input type="text" name="last_name" size="60" value="<?= $ln ?>"></p>
<p>Email: <input type="text" name="email" size="30" value="<?= $e ?>"></p>
<p>Headine: <br><input type="text" name="headline" size="80" value="<?= $h ?>"></p>
<p>Summary: <br><textarea name="summary" rows="8" cols="80"><?= $s ?></textarea></p>
<p>
<input type="submit" name="save" value="Save"/>
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
</div>
</body>
</html>