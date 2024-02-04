<?php

use App\Router\Router;

?>

<body>
    <nav>
        <ul>
            <li><a href="<?= Router::url('admin', ['entity' => 'user', 'action' => 'list']) ?>">Users</a></li>
            <li><a href="<?= Router::url('admin', ['entity' => 'post', 'action' => 'list']) ?>">Posts</a></li>
            <li><a href="<?= Router::url('admin', ['entity' => 'comment', 'action' => 'list']) ?>">Comments</a></li>
            <li><a href="<?= Router::url('admin', ['entity' => 'category', 'action' => 'list']) ?>">Categories</a></li>
        </ul>
    </nav>
    <h1><?= $entityName ?> list</h1>
    <table>
        <thead>
            <tr>
                <?php foreach ($entities[0] as $key => $value) : ?>
                    <th><?= $key ?></th>
                <?php endforeach; ?>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entities as $entity) : ?>
                <tr>
                    <?php foreach ($entity as $key => $value) : ?>
                        <?php if ($key === 'password') : ?>
                            <td>********</td>
                            <?php continue; ?>
                        <?php endif; ?>
                        <?php if (is_array($value)) : ?>
                            <td>
                                <?php echo implode(', ', $value) ?>
                            </td>
                            <?php continue; ?>
                        <?php endif; ?>
                        <td><?= (is_string($value) && strlen($value) > 255) ? substr($value, 0, 50) . '...' : $value ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a href="<?= Router::url('admin-entity', ['action' => 'show', 'entity' => strtolower($entityName), 'id' => $entity['id']]) ?>">Show</a>
                        <a href="<?= Router::url('admin-entity', ['action' => 'delete', 'entity' => strtolower($entityName), 'id' => $entity['id']]) ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>