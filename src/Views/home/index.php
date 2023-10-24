<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Container List</title>
</head>
<body>
<h1>Container List</h1>
<?php if(isset($error)): ?>
    <span><?=$error ?></span>
<?php endif ?>
<table>
    <tr>
        <th>Name</th>
        <th>Width</th>
        <th>Height</th>
        <th>Length</th>
    </tr>
    <?php foreach ($containers as $container): ?>
    <tr>
        <td><?= $container->name ?></td>
        <td><?= $container->width ?></td>
        <td><?= $container->height ?></td>
        <td><?= $container->length ?></td>
    </tr>
    <?php endforeach; ?>
</table>

<?php foreach ($transports as $key => $transport): ?>
<h1>Transport <?= $key + 1 ?></h1>
<table>
    <tr>
        <th>Amount</th>
        <th>Width</th>
        <th>Height</th>
        <th>Length</th>
    </tr>
    <?php foreach ($transport->packages as $package): ?>
        <tr>
            <td><?= $package->amount ?></td>
            <td><?= $package->width ?></td>
            <td><?= $package->height ?></td>
            <td><?= $package->length ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<?php endforeach; ?>

<form method="post" action="/calculate">
    <input type="hidden" name="transports" value="<?= htmlspecialchars(json_encode($transports)) ?>">
    <input type="hidden" name="containers" value="<?= htmlspecialchars(json_encode($containers)) ?>">
    <input type="submit" name="calculate" value="Calculate">
</form>
</body>
</html>