<body>
    <h1><?= $entityName ?> list</h1>
    <table>
        <thead>
            <tr>
                <?php

                use App\Router\Router;

                foreach ($entities[0] as $key => $value) : ?>
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
                        <td><?= $value ?></td>
                    <?php endforeach; ?>
                    <td>
                        <a href="<?= Router::url('admin-entity', ['action' => 'show', 'entity' => strtolower($entityName), 'id' => $entity['id']]) ?>">Show</a>
                        <a href="/admin/edit/<?= $entity['id'] ?>">Edit</a>
                        <a href="/admin/delete/<?= $entity['id'] ?>">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>