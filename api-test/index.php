<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Cuaca</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Prakiraan Cuaca</h2>

    <?php
    require_once __DIR__ . '/classes/Controller.php';

    $controller = new Controller();

    $lat = -8.157654;
    $long = 113.722846;

    $weather = $controller->getWeather($lat, $long);

    if (isset($weather['status']) && $weather['status'] == 'success') {
        $cw = $weather['data'];

        echo '<div class="row g-4">';
        foreach ($cw['forecast'] as $data) { ?>
            <div class="col-md-6 col-lg-4">
                <div class="card p-3 shadow-sm">
                    <h3><?= $data['temperature'] ?> °C</h3>
                    <p><?= $data['weather'] ?></p>
                    <small><?= $data['local_datetime'] ?></small>
                </div>
            </div>
        <?php }
        echo '</div>';

    } else {
        echo "<p style='color:red;text-align:center;'>Gagal ambil data cuaca</p>";
    }
    ?>
</div>

</body>
</html>