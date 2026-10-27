<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Informasi Cuaca</title>
    <!-- Menambahkan Bootstrap CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <h2 class="text-center mb-4">Prakiraan Cuaca</h2>

    <?php
    require_once '../classes/Controller.php';

    $controller = new Controller();
    // Koordinat contoh: Gedung JTI Polije
    $lat = -8.157654;
    $long = 113.722846;

    $weather = $controller->getWeather($lat, $long);

    if (isset($weather['status']) && $weather['status'] == 'success') {
        $cw = $weather['data'];
        echo '<div class="row g-4">'; // Container untuk grid Bootstrap
        
        foreach ($cw['forecast'] as $data) { ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h1 class="card-title text-primary"><?= $data['temperature'] ?> °C</h1>
                        <h3 class="card-title h5"><?= $data['weather'] ?></h3>
                        <h6 class="card-subtitle mb-2 text-muted">
                            <?= $cw['location'] ?>
                        </h6>
                        <p class="card-text small text-secondary">
                            <?= $data['local_datetime'] ?>
                        </p>
                        <ul class="list-group list-group-flush mt-3">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Humidity <span><?= $data['humidity'] ?>%</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Wind Speed <span><?= $data['wind_speed'] ?> km/h</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Wind Direction <span><?= $data['wind_direction'] ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        <?php } 
        
        echo '</div>';
    } else {
        echo '<div class="alert alert-danger text-center" role="alert">
                Data tidak tersedia atau terjadi kesalahan koneksi.
              </div>';
    }
    ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>