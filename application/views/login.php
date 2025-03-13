<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Login</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    

</head>
<body>

<div id="container">
    <div class='row'>
        <div class='col-6 offset-3'>
                    
            <h1>App Shaggy Owl</h1>

            <form action="/login/auth" method="post">

            <label for="username">Username:</label>
            <input type="text" name="username" id="username" /><br />

            <label for="password">Password:</label>
            <input type="password" name="password" id="password" /><br />

            <input type="submit" value="Login" />
            

            <?php if($this->session->flashdata('error')): ?>
                <p><?php echo $this->session->flashdata('error'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>