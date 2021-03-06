<?php 
include 'layouts/header.php';
$logged_in = false;
include 'layouts/nav.php';
include 'config.php';


?>
<div  style="margin-bottom: 150px;"></div>
<div class="container">
    <div class="row justify-content-center">
<!--  create new password form -->

<?php if(isset($_GET['reset'])) : ?>

<form action="" method="post">
    <h5>Enter your Email</h5>
    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" name="email" class="form-control" required>
    </div>

    <input type="submit" class="btn btn-primary btn-block" name="newPass" value="Continue to Reset">
</form>

<?php 
if(isset($_POST['newPass'])){
    extract($_REQUEST);
    if(empty($email)){
        return header('location: reset_pwd.php?emptyfields');
     }
     $sql = "SELECT * FROM users WHERE email=?";
     // echo $session_id;
     $stmt = $conn->prepare($sql); 
     $stmt->bind_param("s", $email);
     $stmt->execute();
     $result = $stmt->get_result();
     $data = $result->fetch_all(MYSQLI_ASSOC);
     if($data) {
         foreach($data as $row){
             session_start();
             $_SESSION['id'] = $row['id'];
             $_SESSION['username'] = $row['username'];
             
             header('location: reset_pwd.php?create_pwd');
         }
     }
}
?>

<?php  
else :
if(isset($_GET['create_pwd'])):
    session_start();
    $id = $_GET['create_pwd'] ?? '';
?>


<form action="process.php" method="post">
    <h5>Create new password</h5>
    <div class="form-group">
        <label for="password">Password</label>
        <input type="password" name="password" class="form-control" minlength="8" required>
    </div>
    <div class="form-group">
        <label for="password">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" minlength="8" required>
    </div>

    <input type="hidden" name="id" value="<?= $_SESSION['id'] ?? $id ?>">
    <input type="hidden" name="username" value="<?= $_SESSION['username'] ?>">

    <input type="submit" class="btn btn-primary btn-block" name="pwd_reset" value="Reset Password">
</form>
<?php endif ?>
</div>
</div>
<?php endif; ?>