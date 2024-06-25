<nav>
  <div class='container'>
    <div class='pages'>
      <a href='index.php'> Home </a>
    </div>

    <div class='profile'>
      <?php if(isset($_SESSION['userID'])) :?>
        <a href='view-account.php'>Profile</a>
      <?php else :?>
        <a href='login.php'>Login</a>
        <a href='create-account.php'>Sign Up</a>
      <?php endif ?>
    </div>
  </div>
</nav>