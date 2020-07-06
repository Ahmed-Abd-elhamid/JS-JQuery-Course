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

       for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['edu_year'.$i]) ) continue;
        if ( ! isset($_POST['edu_school'.$i]) ) continue;

        $edu_year = $_POST['edu_year'.$i];
        $edu_school = $_POST['edu_school'.$i];

        if ( strlen($edu_year) == 0 || strlen($edu_school) == 0 ) {
            $_SESSION['error'] = "All fields are required";
            header("Location: add.php");
            return;
        }

        if ( ! is_numeric($edu_year) ) {
            $_SESSION['error'] = "Education year must be numeric";
            header("Location: add.php");
            return;
        }
    }

       for($i=1; $i<=9; $i++) {
        if ( ! isset($_POST['year'.$i]) ) continue;
        if ( ! isset($_POST['desc'.$i]) ) continue;

        $year = $_POST['year'.$i];
        $desc = $_POST['desc'.$i];

        if ( strlen($year) == 0 || strlen($desc) == 0 ) {
            $_SESSION['error'] = "All fields are required";
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }

        if ( ! is_numeric($year) ) {
            $_SESSION['error'] = "Position year must be numeric";
            header("Location: edit.php?profile_id=".$_GET['profile_id']);
            return;
        }
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

        $profile_id = $_GET['profile_id'];
        
        $stmt = $pdo->prepare('DELETE FROM Education WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $profile_id));

        $rank = 1;
        for($i=1; $i<=9; $i++) {
          if ( ! isset($_POST['edu_year'.$i]) ) continue;
          if ( ! isset($_POST['edu_school'.$i]) ) continue;
        
          $edu_year = $_POST['edu_year'.$i];
          $edu_school = $_POST['edu_school'.$i];

          $stmt = $pdo->prepare('SELECT institution_id FROM Institution WHERE name = :prefix');
            $stmt->execute(array( ':prefix' => $edu_school ));
            $retval = array();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( empty($row) ){
          $stmt = $pdo->prepare('INSERT INTO Institution (name)
          VALUES ( :edu_school)');
        
          $stmt->execute(array(
          ':edu_school' => $edu_school)
          );             

          $institution_id = $pdo->lastInsertId();
        }else{
          $institution_id = $row['institution_id'];
        }

          $stmt = $pdo->prepare('INSERT INTO Education
            (profile_id, rank, year, institution_id)
            VALUES ( :pid, :rank, :edu_year, :edu_school)');
        
          $stmt->execute(array(
          ':pid' => $profile_id,
          ':rank' => $rank,
          ':edu_year' => $edu_year,
          ':edu_school' => $institution_id)
          );
        
          $rank++;
        
        }
        
        $stmt = $pdo->prepare('DELETE FROM Position WHERE profile_id=:pid');
        $stmt->execute(array( ':pid' => $profile_id));

        $rank = 1;
        for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
        
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            $stmt = $pdo->prepare('INSERT INTO Position
            (profile_id, rank, year, description)
            VALUES ( :pid, :rank, :year, :desc)');
        
            $stmt->execute(array(
            ':pid' => $_REQUEST['profile_id'],
            ':rank' => $rank,
            ':year' => $year,
            ':desc' => $desc)
            );
            $rank++;
        }

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
<title>Ahmed Abdelhamid - a9b3c24a</title>
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
<p>Education: <input type="button" value="+" onclick="addEdu();return false;"></p>
<div id="education_fields">
<?php 
    $stmt = $pdo->prepare("SELECT * FROM Education where profile_id = :xz");
    $stmt->execute(array(":xz" => $_GET['profile_id']));
    $lenEdu = 0;
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)){
        $lenEdu ++;
        $stt = $pdo->prepare('SELECT name FROM Institution WHERE institution_id = :prefix');
        $stt->execute(array( ':prefix' => $rw['institution_id'] ));
        $retval = array();
        $row = $stt->fetch(PDO::FETCH_ASSOC);
        $school = $row['name'];
        ?>
            <div id="education<?= htmlentities($rw['rank'])?>">
            <p>Year: <input type="text" name="edu_year<?= $rw['rank']?>" value="<?= htmlentities($rw['year'])?>">
            <input type="button" value="-" onclick="$('#education<?= htmlentities($rw['rank'])?>').remove();return false;"></p>
            <p>School: <input type="text" size="80" name="edu_school<?= htmlentities($rw['rank'])?>" class="school" value="<?= htmlentities($school)?>" /></p>
            </div>
        <?php
    }
?>
</div>

<p>Position: <input type="button" value="+" onclick="addPos();return false;"></p>
<div id="position_fields">
<?php 
    $stmt = $pdo->prepare("SELECT * FROM Position where profile_id = :xz");
    $stmt->execute(array(":xz" => $_GET['profile_id']));
    $lenPos = 0;
    while ($rw = $stmt->fetch(PDO::FETCH_ASSOC)){
        $lenPos ++;
        ?>
            <div id="position<?= htmlentities($rw['rank'])?>">
            <p>Year: <input type="text" name="year<?= $rw['rank']?>" value="<?= htmlentities($rw['year'])?>">
            <input type="button" value="-" onclick="$('#position<?= htmlentities($rw['rank'])?>').remove();return false;"></p>
            <textarea name="desc<?= htmlentities($rw['rank'])?>" rows="8" cols="80"><?= htmlentities($rw['description'])?></textarea>    
            </div>
        <?php
    }
?>
</div>
<input type="submit" name="save" value="Save"/>
<input type="submit" name="cancel" value="Cancel"/>
</p>
</form>
</div>
<script>
  var countPos = <?= $lenPos ?>;
  var countEdu = <?= $lenEdu ?>;

  function addPos() {
  const div = document.createElement('div');
  countPos ++;
  if ( countPos < 10 ){
    div.id = `position${countPos}`;
    div.innerHTML = `
    <p>Year: <input type="text" name="year${countPos}" value="">
    <input type="button" value="-" onclick="$('#position${countPos}').remove();return false;"></p>
    <textarea name="desc${countPos}" rows="8" cols="80"></textarea>
    `;

    document.getElementById('position_fields').appendChild(div);
  }else{
    alert("Maximum of nine position entries exceeded");
  }
}

function addEdu() {
    const div = document.createElement('div');
    countEdu ++;
    if ( countEdu < 10 ){
        div.id = `education${countEdu}`;
        div.innerHTML = `
        <p>Year: <input type="text" name="edu_year${countEdu}" value="">
        <input type="button" value="-" onclick="$('#education${countEdu}').remove();return false;"></p>
        <p>School: <input type="text" size="80" name="edu_school${countEdu}" class="school" value="" /></p>
        `;

        document.getElementById('education_fields').appendChild(div);
    }else{
        alert("Maximum of nine education entries exceeded");
    }
    $('.school').autocomplete({ source: "school.php" });
}

$('.school').autocomplete({ source: "school.php" });

</script>
</div>
</body>
</html>