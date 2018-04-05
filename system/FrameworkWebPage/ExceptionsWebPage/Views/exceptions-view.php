<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>MyFrameWork - Error</title>

</head>

<body>

<header>
    <nav class="navbar navbar-light bg-light" style="margin-bottom: 100px;">
        <span class="navbar-brand mb-0 h1">MyFrameWork</span>
    </nav>
</header>

<div class="container">

    <div class="card">
        <div class="card-header bg-danger text-white">
            <h2><?= get_class($e) ?></h2>
        </div>
        <div class="card-body">
            <h5 class="card-title text-danger"><?= $e->getMessage() ?></h5>
            <p class="card-text"><?= $e->getFile() ?></p>
        </div>
    </div>

    <div class="list-group">
        <?php foreach ($e->getTrace() as $num => $trace): ?>
            <a href="#" class="list-group-item list-group-item-action flex-column align-items-start">
                <div class="d-flex w-100 justify-content-between">
                    <?php

                    $function = "";
                    if(isset($trace['class'])) $function .= $trace['class'];
                    if(isset($trace['type'])) $function .= $trace['type'];
                    if(isset($trace['function'])) $function .= $trace['function'];

                    ?>
                    <h5 class="mb-1"><b>#<?= $num ?> </b><?= $function ?></h5>
                    <small>line <?= $trace['line'] ?></small>
                </div>
                <p class="mb-1"><?= $trace['file'] ?></p>
                <!--<small>Donec id elit non mi porta.</small>-->
            </a>
        <?php endforeach; ?>
    </div>

</div>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>