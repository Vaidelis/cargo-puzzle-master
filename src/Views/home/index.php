<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Container List</title>
    <link rel="stylesheet" type="text/css" href="../../../public/css/style.css">
</head>
<body>
<div class="container">
<?php if(isset($error)): ?>
    <span class="error-message"><?=$error ?></span>
<?php endif ?>
    <h1 class="table-text">Container List</h1>
<table class="information-list">
    <tr>
        <th>Name</th>
        <th>Width (cm)</th>
        <th>Height (cm)</th>
        <th>Length (cm)</th>
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
<h1 class="table-text">Transport <?= $key + 1 ?></h1>
<table class="information-list">
    <tr>
        <th>Amount</th>
        <th>Width (cm)</th>
        <th>Height (cm)</th>
        <th>Length (cm)</th>
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

<form class="button-place" method="post" action="/calculate">
    <input type="hidden" name="transports" value="<?= htmlspecialchars(json_encode($transports)) ?>">
    <input type="hidden" name="containers" value="<?= htmlspecialchars(json_encode($containers)) ?>">
    <input type="submit" name="calculate" value="Calculate">
</form>
</body>
</div>
</html>