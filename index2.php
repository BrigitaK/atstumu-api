<?php 
session_start();


//postas
if(!empty($_POST)){
    $town1 = $_POST['m1'];
    $town2 = $_POST['m2'];

    // CATCHE START
    include __DIR__ . '/Catche.php';
    $data = new Catche;

    $answer = $data->get();

    $_SESSION['method'] = false === $answer ? 'API' : 'CATCHE';
        // API START
    if (false === $answer) {
        $ch = curl_init();

        curl_setopt(
        $ch, CURLOPT_URL, 
        'https://www.distance24.org/route.json?stops='.$town1.'|'.$town2
        );//pasako kur mes eisime
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);//returninam

        $answer = curl_exec($ch); // siuntimas ir laukimas atsakymo

        $answer = json_decode($answer);

        //$DATA->set($answer); // <---- uzkesinam naujus duomenis

        $dist = $answer->distance;

    }
    //api end
    //jeigu siunciam tai post, jei norim kazka gauti, tai get

    $_SESSION['distance'] = $dist;
    $_SESSION['t1'] = $town1;
    $_SESSION['t2'] = $town2;
    $_SESSION['img1'] = $answer->stops[0]->wikipedia->image;
    $_SESSION['img2'] = $answer->stops[1]->wikipedia->image;
    header ('Location: http://localhost:8888/dashboard/agurkai/atstumu-api/atstumu-api/index2.php');
    die;
}
//atsakymai
if(isset($_SESSION['distance'])){
    $dist = $_SESSION['distance'];
    $town1 = $_SESSION['t1'];
    $town2 = $_SESSION['t2'];
    $img1 = $_SESSION['img1'];
    $img2 = $_SESSION['img2'];
    $method = $_SESSION['method'];
    unset($_SESSION['distance'], $_SESSION['t1'], $_SESSION['t2'], $_SESSION['img1'], $_SESSION['img2'],$_SESSION['method']);
}

//tam kad po issiuntimo gauti atsakyma, persikrovus psl norim kad liktu irasyti miestai naudojam inpute value


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API</title>
</head>
<body>
<h1>Atstumų API</h1>
    <form action="" method="post">
    
    <input type="text" name="m1" value="<?= $town1 ?? ''?>">
    <input type="text" name="m2" value="<?= $town2 ?? ''?>">

    <button type="submit">Gauti atstumą</button>
    </form>

    <?php if(isset($dist)) {?>
    <h2> Būdas: <?= $method ?> </h2>
    <h2>Atstumas yra: <?= $dist?> km.</h2>
    <img style="height: 300px;" src="<?= $img1 ?? ''?>" alt="">
    <img style="height: 300px;" src="<?= $img2 ?? '' ?>" alt="">
    <?php } ?>
</body>
</html>