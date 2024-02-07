<?php

use App\Model\PostModel;
use App\Router\Router;
use App\Model\UserModel;
use App\Controller\UserController;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\Class\Database;
use App\Repository\UserRepository;
use App\Service\UserService;

/** @var PostModel $post */
$post;

$db = new Database();
$connection = $db->getConnection();

$categoryService = new CategoryService(new CategoryRepository($connection));
$userService = new UserService(new UserRepository($connection));

$category = $categoryService->getCategoryById($post->getCategoryId());
$user = $userService->getUserById($post->getUserId());

?>

<body>
    <h1><?= $post->getTitle() ?></h1>
    <p>Écrit par : <?= $user->getFirstName() ?> <?= $user->getLastName() ?></p> 
    <p>Catégorie : <?= $category->getName() ?></p>
    <p><?= $post->getContent() ?></p>
    <p><?= $post->getCreatedAt()->format('d/m/Y') ?></p>
    <div>
        <h2>Commentaires</h2>
        <?php foreach ($post->getComments() as $comment) : ?>
            <?php $user = new UserModel($comment->getUserId()) ?>
            <p><?= $comment->getContent() ?></p>
            <p><?= $user->getFirstname() ?> <?= $user->getLastname() ?></p>
            <p><?= $comment->getCreatedAt()->format('d/m/Y') ?></p>
        <?php endforeach; ?>
        <?php if (UserController::getUser()) : ?>
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