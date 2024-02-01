<?php

use App\Router\Router;

?>

<body>
    <h1>Register</h1>
    <?php if (isset($error)) : ?>
        <p><?= $error ?></p>
    <?php endif ?>
    <form action="<?= Router::url('register') ?>" method="post">
        <div>
            <label for="email">Email</label>
            <input type="text" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <label for="password_confirm">Password Confirm</label>
            <input type="password" name="password_confirm" id="password_confirm" required>
        </div>
        <div>
            <label for="firstname">Firstname</label>
            <input type="text" name="firstname" id="firstname" required>
        </div>
        <div>
            <label for="lastname">Lastname</label>
            <input type="text" name="lastname" id="lastname" required>
        </div>
        <div>
            <input type="submit" value="Register">
        </div>
    </form>
</body>