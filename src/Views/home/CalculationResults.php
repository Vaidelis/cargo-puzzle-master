<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Container List</title>
    <link rel="stylesheet" type="text/css" href="../../../public/css/style.css">
</head>
<body>
<div class="container">
<h1>Used containers list for each transport</h1>

<?php foreach ($containers_list as $key => $containers): ?>
    <h1 class="table-text">Transport <?= $key + 1 ?></h1>
    <table class="information-list">
        <tr>
            <th>Container name</th>
            <th>Width (cm)</th>
            <th>Height (cm)</th>
            <th>Length (cm)</th>
            <th>Place filled (%)</th>
        </tr>
        <?php foreach ($containers as $container): ?>
            <tr>
                <td><?= $container['name'] ?></td>
                <td><?= $container['width'] ?></td>
                <td><?= $container['height'] ?></td>
                <td><?= $container['length'] ?></td>
                <td><?= $container['place_filled'] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endforeach; ?>
    <div class="button-place">
         <a class="back-button" href="/">Back</a>
    </div>
</div>
</body>
</html>