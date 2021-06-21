<div>
    <?php foreach ($model as $key => $prop) : ?>
        <?php if ($prop['props']): ?>
            <div><b><?= $prop['props']['name'] ?></b> có <b><?= $prop['count'] ?></b> đối tượng</div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>