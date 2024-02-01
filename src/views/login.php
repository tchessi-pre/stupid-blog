<?php

use App\Router\Router;

?>

<body>

    <h1>Login</h1>

    <?php if (isset($error)) : ?>
        <p><?= $error ?></p>
    <?php endif ?>

    <form action="<?= Router::url('login') ?>" method="post">
        <div>
            <label for="email">Email</label>
            <input type="text" name="email" id="email" required>
        </div>
        <div>
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div>
            <input type="submit" value="Login">
        </div>
    </form>

</body>