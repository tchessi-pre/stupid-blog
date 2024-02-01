<?php

use App\Class\Controller;
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
    <h1>Stupid Blog</h1>
    <?php if (Controller::getUser()) : ?>
        <p>Bonjour <?= Controller::getUser()->getFirstname() ?> <?= Controller::getUser()->getLastname() ?></p>
    <?php endif ?>
    <nav>
        <ul>
            <li><a href="<?= Router::url('home') ?>">Accueil</a></li>
            <li><a href="<?= Router::url('posts') ?>">Articles</a></li>
            <?php if (null !== Controller::getUser()) : ?>
                <li><a href="<?= Router::url('profile') ?>">Profil</a></li>
                <li><a href="<?= Router::url('logout') ?>">Se déconnecter</a></li>
            <?php else : ?>
                <li><a href="<?= Router::url('login') ?>">Se connecter</a></li>
                <li><a href="<?= Router::url('register') ?>">S'inscrire</a></li>
            <?php endif ?>
        </ul>
    </nav>
</header>