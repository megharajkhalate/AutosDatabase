<?php
require_once "pdo.php";
session_start();



if ( isset($_POST['make']) && isset($_POST['model']) && isset($_POST['year'])
     && isset($_POST['mileage']) && isset($_POST['auto_id']) ) {

    // Data validation
    if ( strlen($_POST['make']) < 1 || strlen($_POST['mileage']) < 1) {
        $_SESSION['error'] = 'Missing data';
        header("Location: edit.php?auto_id=".$_POST['auto_id']);
        return;
    }

    if ( strpos($_POST['year']) === false ) {
        $_SESSION['error'] = 'Bad data';
        header("Location: edit.php?auto_id=".$_POST['auto_id']);
        return;
    }

    $sql = "UPDATE autos SET make = :make,model = :model,
            year = :year, mileage = :mileage
            WHERE auto_id = :auto_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':make' => $_POST['make'],
        ':model' => $_POST['model'],

        ':year' => $_POST['year'],
        ':mileage' => $_POST['mileage'],
        ':auto_id' => $_POST['auto_id']));
    $_SESSION['success'] = 'Record updated';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that auto_id is present
if ( ! isset($_GET['auto_id']) ) {
  $_SESSION['error'] = "Missing auto_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT * FROM autos where auto_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['auto_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for auto_id';
    header( 'Location: index.php' ) ;
    return;
}

// Flash pattern
if ( isset($_SESSION['error']) ) {
    echo '<p style="color:red">'.$_SESSION['error']."</p>\n";
    unset($_SESSION['error']);
}

$n = htmlentities($row['make']);
$m = htmlentities($row['model']);

$e = htmlentities($row['year']);
$p = htmlentities($row['mileage']);
$auto_id= $row['auto_id'];
?>

<!DOCTYPE html>
<html>
    <head>

    <link  type="text/css" rel="stylesheet" href="style.css">

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body>
<div class="heading"><h2>Edit autos</h2>
<form method="post">
<p>make:
<input type="text" name="make" value="<?= $n ?>"></p>
<p>model:
<input type="text" name="model" value="<?= $n ?>"></p>
<p>year:
<input type="text" name="year" value="<?= $e ?>"></p>
<p>mileage:
<input type="text" name="mileage" value="<?= $p ?>"></p>
<input type="hidden" name="auto_id" value="<?= $auto_id ?>">
<p><input type="submit" value="Save"/>
<a href="index.php">Cancel</a></p>
</div>
</form>
</body>
</html>