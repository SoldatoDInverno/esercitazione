<?php
$prodotto = "";
$prezzo = "";
$sconto = ""; 
$erroreprodotto = null;
$erroreprezzo = null;
$erroresconto = null;
$risultato = null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prodotto = isset($_POST["prodotto"]) ? trim(htmlspecialchars($_POST["prodotto"])) : "";
    $prezzo = isset($_POST["prezzo"]) ? floatval($_POST["prezzo"]) : 0;
    $sconto =isset($_POST["sconto"]) ? floatval($_POST["sconto"]) : "";
    if (empty($prodotto) || strlen($prodotto) > 30) {
        $erroreprodotto = "Il prodotto deve essere inserito (max 30 caratteri)";
    }
    if (!is_numeric($prezzo) || $prezzo <= 0) {
        $erroreprezzo = "Il prezzo deve essere maggiore di 0";
    }
    if ($sconto === "" || $sconto < 1 || $sconto > 90) {
        $erroresconto = "Inserisci uno sconto valido (da 1 a 90)";
    }
    if (!$erroreprodotto && !$erroreprezzo && !$erroresconto) {
        $risparmio = $prezzo * ($sconto / 100);
        $prezzo_finale = $prezzo - $risparmio;

        if ($sconto < 20) {
            $fascia = "PICCOLO";
            $classe_fascia = "piccolo";
        } elseif ($sconto <= 49) {
            $fascia = "BUONO";
            $classe_fascia = "buono";
        } else {
            $fascia = "SUPER";
            $classe_fascia = "super";
        }

        $risultato = ['prodotto'      => htmlspecialchars($prodotto),
        'risparmio'     => number_format($risparmio, 2, ',', '.'),
        'prezzo_finale' => number_format($prezzo_finale, 2, ',', '.'),
        'fascia'        => $fascia,
        'classe_fascia' => $classe_fascia
        ];
    }
    $log = fopen("log.csv", "a");
    if ($log) {
        $record = date("d/m/Y") . ";" . 
                date("H:i:s") . ";" . 
                (!empty($prodotto) ? $prodotto : "N/D") . ";" . 
                (!$erroreprezzo && is_numeric($prezzo) ? number_format($prezzo, 2, '.', '') : "N/D") . ";" . 
                (!$erroresconto && is_numeric($sconto) ? $sconto : "N/D") . ";" . 
                (!$erroreprodotto && !$erroreprezzo && !$erroresconto ? number_format($risparmio, 2, '.', '') : "N/D") . ";" . 
                (!$erroreprodotto && !$erroreprezzo && !$erroresconto ? number_format($prezzo_finale, 2, '.', '') : "N/D") . ";" . 
                (!$erroreprodotto && !$erroreprezzo && !$erroresconto ? $fascia : "N/D") . "\n";             
        fwrite($log, $record);
        fclose($log);
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
    <form method="post" action="index.php" novalidate>
        <label for="prodotto">Prodotto (Max 30 char)</label>
        <input type="text" id="prodotto" name="prodotto" class="<?php if ($erroreprodotto != null) { echo "errore"; } ?>" value="<?php echo htmlspecialchars($prodotto); ?>" maxlength="30">
        <p class="msg-errore"><?php if ($erroreprodotto != null) { echo $erroreprodotto; } ?></p>

        <label for="prezzo">Prezzo (€)</label>
        <input type="number" id="prezzo" name="prezzo" class="<?php if ($erroreprezzo != null) { echo "errore"; } ?>" value="<?php echo htmlspecialchars($prezzo); ?>">
        <p class="msg-errore"><?php if ($erroreprezzo != null) { echo $erroreprezzo; } ?></p>

        <label for="sconto">Sconto (%)</label>
        <input type="number" id="sconto" name="sconto" class="<?php if ($erroresconto != null) { echo "errore"; } ?>" value="<?php echo htmlspecialchars($sconto); ?>">
        <p class="msg-errore"><?php if ($erroresconto != null) { echo $erroresconto; } ?></p>

        <button type="submit">Calcola</button>
    </form>

    <?php if ($risultato): ?>
    <div class="card <?php echo $risultato['classe_fascia']; ?>">
        <h2>Prodotto: <?php echo $risultato['prodotto']; ?></h2>
        <p>Risparmio: <strong>€ <?php echo $risultato['risparmio']; ?></strong></p>
        <p>Prezzo Finale: <strong>€ <?php echo $risultato['prezzo_finale']; ?></strong></p>
        <p>Fascia sconto: <strong><?php echo $risultato['fascia']; ?></strong></p>
    </div>
    <?php endif; ?>
</body>
</html>