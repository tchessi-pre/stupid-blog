<?php

use App\Class\Controller;
use App\Model\PostModel;
use App\Router\Router;
use App\Class\User;
use App\Controller\CategoryController;
use App\Model\CategoryModel;
use App\Model\UserModel;
use App\Controller\UserController;
use App\Repository\CategoryRepository;
use App\Service\CategoryService;
use App\Class\Database;

/** @var PostModel $post */
$post;

$db = new Database();
$connection = $db->getConnection();
$categoryRepository = new CategoryRepository($connection);
$categoryService = new CategoryService($categoryRepository);
$category = $categoryService->getCategoryById($post->getCategoryId());


?>

<body>
    <h1><?= $post->getTitle() ?></h1>
    <?php $userPost = new PostModel($post->getUserId()); var_dump($userPost)?>
    <p>Écrit par : </p>
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