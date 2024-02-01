<?php

use App\Class\Controller;
use App\Router\Router;

?>

<body>
    <h1>Bonjour, <?= Controller::getUser()->getFirstname() ?> <?= Controller::getUser()->getLastname() ?> bienvenu.e sur ton profil</h1>
    <p>Voici tes informations personnelles :</p>
    <div>
        <p>Email : <?= Controller::getUser()->getEmail() ?></p>
        <p>Prénom : <?= Controller::getUser()->getFirstname() ?></p>
        <p>Nom : <?= Controller::getUser()->getLastname() ?></p>
    </div>
    <form action="<?= Router::url('profile') ?>" method="post">
        <div>
            <label for="email">Email</label>
            <input type="text" name="email" id="email" value="<?= Controller::getUser()->getEmail() ?>" required>
        </div>
        <div>
            <label for="firstname">Prénom</label>
            <input type="text" name="firstname" id="firstname" value="<?= Controller::getUser()->getFirstname() ?>" required>
        </div>
        <div>
            <label for="lastname">Nom</label>
            <input type="text" name="lastname" id="lastname" value="<?= Controller::getUser()->getLastname() ?>" required>
        </div>
        <div>
            <input type="submit" value="Modifier">
        </div>
    </form>
    <div>
        <a href="<?= Router::url('logout') ?>">Se déconnecter</a>
    </div>
</body>