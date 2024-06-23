<?php 
$name = getenv('MYNAME');

session_start();
// redirect if user is already logged in
if(isset($_SESSION['user'])){
  header("Location:index.php");
  exit();
}

$username = $_POST['username'] ?? "";
$passwordOne = $_POST['passwordOne'] ?? "";

$error = '';
if(isset($_POST['submit'])){

  if (empty($username)){ $error = 'no username given'; }
  else if (empty($passwordOne)) { $error = 'no password given'; }

}
 
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./styles/login.css">
</head>

<body>
  <div class="loginForm">

    <h1>Login</h1>

    <form id="create-account" method="post" action="">

      <div class="textInput">
        <input 
        type="text" 
        name="username"
        value="<?= $username ?>" 
        placeholder=' '
        />
        <label for="username">Username</label>
      </div>

      <div class="textInput">
        <input 
        type="password" 
        name="passwordOne" 
        value="<?= $passwordOne ?>"
        placeholder=' '
        />
        <label for="password">Password</label>
      </div>

      <div class="checkboxInput">
        <input type="checkbox"
        name="rememberMe"
        value="1"/>
        <label for="remember">Remember Me</label>
      </div>

      <input type="submit" name="submit" value="Create Account">

    </form>

    <?php if ($error != ''): ?>
      <p class="error"> error: <?= $error ?> </p>
    <?php endif ?>

    <p>Dont have an account?</p>
    <a href='/~<?= $name ?>/3430/assn/cois-3430-2024su-a2-BigBeill/create-account'>create account</a>

  </div>
</body>

</html>