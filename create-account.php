<?php $name = getenv('MYNAME'); ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account</title>
  <link rel="stylesheet" href="./styles/login.css">
</head>

<body>
  <div class="loginForm">

    <h1>Create Account</h1>

    <div class="textInput">
      <input 
      type="text" 
      name="username" 
      placeholder=' '
      />
      <label for="username">Username</label>
    </div>

    <div class="textInput">
      <input 
      type="text" 
      name="email" 
      placeholder=' '
      />
      <label for="email">Email</label>
    </div>

    <div class="textInput">
      <input 
      type="password" 
      name="passwordOne" 
      placeholder=' '
      />
      <label for="password">Password</label>
    </div>

    <div class="textInput">
      <input 
      type="password" 
      name="passwordTwo" 
      placeholder=' '
      />
      <label for="password">Confirm Password</label>
    </div>

    <div class="checkboxInput">
      <input type="checkbox"
      name="rememberMe"
      value="1"/>
      <label for="remember">Remember Me</label>
    </div>

    <input type="submit" value="Create Account">

    <p>Already have an account?</p>
    <a href='/register'>account login</a>

  </div>
</body>

</html>