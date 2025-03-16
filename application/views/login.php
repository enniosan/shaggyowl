<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="it">

<head>    
    <meta charset="utf-8">
    <title>Login</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" await>
    <script src="/assets/js/login.js" defer></script>
    
</head>

<body>

    <div class="login-container">

        <?php echo form_open('login/auth', array('id' => 'loginForm')); ?>
        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
        
        <div class='login-box'>
                
            <h2>DEMO SHAGGY OWL</h2>

            <div class='fields'>
                <label for="username">Username:</label>        
                <input type="text" name="username" id="username" />
        
                <label for="password">Password:</label>
                <input type="password" name="password" id="password" />
            </div>

            <input type="submit" value="Login" />
            
            <div id="login-error" class="error"></div>
        </div>
        <?php echo form_close(); ?>
    </div>

</body>

</html>