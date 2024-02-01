<?php

use App\Router\Router;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stupid Blog</title>
</head>

<header>
    <nav>
        <ul>
            <li><a href="<?= Router::url('login') ?>">Se connecter</a></li>
            <li><a href="<?= Router::url('register') ?>">S'inscrire</a></li>
        </ul>
    </nav>
</header>