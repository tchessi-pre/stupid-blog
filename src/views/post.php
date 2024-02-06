<?php

use App\Class\Controller;
use App\Class\Post;
use App\Router\Router;
use App\Class\User;

/** @var Post $post */
$post;

?>

<body>
    <h1><?= $post->getTitle() ?></h1>
    <p>Écrit par : <?= $post->getUser()->getFirstname() ?> <?= $post->getUser()->getLastname() ?></p>
    <p>Catégorie : <?= $post->getCategory()->getName() ?></p>
    <p><?= $post->getContent() ?></p>
    <p><?= $post->getCreatedAt()->format('d/m/Y') ?></p>
    <div>
        <h2>Commentaires</h2>
        <?php foreach ($post->getComments() as $comment) : ?>
        <?php $user = new User($comment->getUserId()) ?>
            <p><?= $comment->getContent() ?></p>
            <p><?= $user->getFirstname() ?> <?= $user->getLastname() ?></p>
            <p><?= $comment->getCreatedAt()->format('d/m/Y') ?></p>
        <?php endforeach; ?>
        <?php if (Controller::getUser()) : ?>
            <?php if (isset($error['error'])) : ?>
                <p><?= $error['error'] ?></p>
            <?php endif; ?>
            <h2>Ajouter un commentaire</h2>
            <form action="<?= Router::url('add_comment', ['post_id' => $post->getId()]) ?>" method="post">
                <textarea name="content" id="content" cols="30" rows="10"></textarea>
                <button type="submit">Envoyer</button>
            </form>
        <?php endif; ?>
    </div>

</body>