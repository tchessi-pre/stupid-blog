<?php

use App\Class\Controller;
use App\Controller\UserController;
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
    
    <?php if (UserController::getUser()) : ?>
        <p>Bonjour <?= UserController::getUser()->getFirstname() ?> <?= UserController::getUser()->getLastname() ?></p>
    <?php endif ?>
    <nav>
        <ul>
            <li><a href="<?= Router::url('home') ?>">Accueil</a></li>
            <li><a href="<?= Router::url('posts', ['page' => 1]) ?>">Articles</a></li>
            <?php if (null !== UserController::getUser()) : ?>
                <li><a href="<?= Router::url('profile') ?>">Profil</a></li>
                <li><a href="<?= Router::url('logout') ?>">Se déconnecter</a></li>
                <?php if (UserController::hasRole('ROLE_ADMIN')) : ?>
                    <li><a href="<?= Router::url('admin', ['action' => 'list', 'entity' => 'user']) ?>">Admin</a></li>
                <?php endif ?>
            <?php else : ?>
                <li><a href="<?= Router::url('login') ?>">Se connecter</a></li>
                <li><a href="<?= Router::url('register') ?>">S'inscrire</a></li>
            <?php endif ?>
        </ul>
    </nav>
</header>