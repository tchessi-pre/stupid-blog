<?php

use App\Router\Router;

?>

<body>
    <h1><?= $entityName ?> id : <?= $entity->getId() ?></h1>

    <form action="<?= Router::url('admin-entity', ['entity' => $entityName, 'action' => 'edit', 'id' => $entity->getId()]) ?>" method="post">
        <?php foreach ($entity->toArray() as $key => $value) : ?>
            <?php if ($key === 'id') : ?>
                <input type="hidden" name="<?= $key ?>" id="<?= $key ?>" value="<?= $value ?>">
                <?php continue; ?>
            <?php endif; ?>
            <?php if ($key === 'password') : ?>
                <div class="form-group">
                    <label for="<?= $key ?>"><?= $key ?></label>
                    <input type="password" name="<?= $key ?>" id="<?= $key ?>" value="<?= $value ?>">
                </div>
                <?php continue; ?>
            <?php endif; ?>
            <?php if (is_array($value)) : ?>
                <div class="form-group">
                    <label for="<?= $key ?>"><?= $key ?></label>
                    <input type="text" name="<?= $key ?>" id="<?= $key ?>" value="<?= implode(', ', $value) ?>">
                </div>
                <?php continue; ?>
            <?php endif; ?>
            <div class="form-group">
                <label for="<?= $key ?>"><?= $key ?></label>
                <?php if (is_string($value) && strlen($value) > 100) : ?>
                    <textarea name="<?= $key ?>" id="<?= $key ?>" cols="30" rows="10"><?= $value ?></textarea>
                    <?php continue; ?>
                <?php else : ?>
                    <input type="text" name="<?= $key ?>" id="<?= $key ?>" value="<?= $value ?>">
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Modifier</button>
    </form>

</body>