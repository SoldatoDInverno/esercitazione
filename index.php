<?php 
$erroreprodotto=null;
$erroreprezzo=null;
$erroresconto=null;
if ($_SERVER["REQUEST_METHOD"]== "POST") {
    $prodotto = isset($_POST["prodotto"]) ? trim(htmlspecialchars($_POST["prodotto"])) : "";
    $prezzo = isset($_POST["prezzo"]) ? floatval($_POST["prezzo"]) : 0;
    $sconto =isset($_POST["sconto"]) ? floatval($_POST["sconto"]) : "";
    if (empty($prodotto) || $prezzo <= 0 || empty($sconto) || $sconto<1 || $sconto>90) {
        $erroreprezzo="Il prezzo deve essere maggiore di 0";
        $erroreprodotto="Il prodotto deve essere inserito";
        $erroresconto="Inserisci uno sconto valido";

    } 
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>
    <h1>Calcolatore di sconto</h1>
    <form method="post" action="index.php">
        <label for="prodotto">Prodotto</label>
        <input type="text" id="prodotto" name="prodotto" require maxlength="30">
        <p class="msg-errore"><?php
        if ($erroreprodotto!= null);
        echo $erroreprodotto;
        ?></p>
        <label for="prezzo">Prezzo (€)</label>
        <input type="number" id="prezzo" name="prezzo" class="errore" require>
        <p class="msg-errore"><?php
        if ($erroreprezzo!= null);
        echo $erroreprezzo;
        ?></p>
        <label for="sconto">Sconto (%)</label>
        <input type="number" id="sconto" name="sconto" require>
        <p class="msg-errore"><?php
        if ($erroresconto!= null);
        echo $erroresconto;
        ?></p>
        <button type="submit">Calcola</button>
    </form>
</body>
</html>