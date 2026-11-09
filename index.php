<?php
$errore = null;
$prodotto = $prezzo = $sconto = $risparmio = $prezzo_finale = $fascia = $classe_fascia = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $prodotto = isset($_POST['prodotto']) ? trim(htmlspecialchars($_POST['prodotto'])) : '';
    $prezzo = isset($_POST['prezzo']) ? floatval($_POST['prezzo']) : 0;
    $sconto = isset($_POST['sconto']) ? intval($_POST['sconto']) : 0;
    if (empty($prodotto) || strlen($prodotto) > 30 || $prezzo <= 0 || $sconto < 1 || $sconto > 90) {
        $errore = "Per favore, inserisci dati validi prima di procedere.";
        $log_prodotto = empty($prodotto) ? "NOT_VALID" : $prodotto;
        $log_prezzo   = ($prezzo <= 0) ? "NOT_VALID" : $prezzo;
        $log_sconto   = ($sconto < 1 || $sconto > 90) ? "NOT_VALID" : $sconto;
        $log_risparmio = "NOT_VALID";
        $log_finale   = "NOT_VALID";
        $log_fascia   = "NOT_VALID";
    } else {
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
        $log_prodotto  = $prodotto;
        $log_prezzo    = $prezzo;
        $log_sconto    = $sconto;
        $log_risparmio = round($risparmio, 2);
        $log_finale    = round($prezzo_finale, 2);
        $log_fascia    = $fascia;
    }
    $log = fopen("log.csv", "a");
    $record = date("d/m/Y").",".date("H:i:s").",".$_SERVER["REMOTE_ADDR"].",".$log_prodotto.",".$log_prezzo.",".$log_sconto.",".$log_risparmio.",".$log_finale.",".$log_fascia."\n";
    fwrite($log, $record);
    fclose($log);
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calcolatore di sconto</title>
    <link rel="stylesheet" href="stile.css">
</head>
<body>
    <div class="box">
        <h1>Calcolatore di sconto</h1>
        <?php if ($_SERVER["REQUEST_METHOD"] == "POST"): ?>
            <?php if ($errore): ?>
                <div class="card errore">
                    <p style="margin: 0; font-weight: bold; color: #B32D2D;">
                        <?php echo $errore; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="card <?php echo $classe_fascia; ?>">
                    <p><strong>Prodotto:</strong> <?php echo $prodotto; ?></p>
                    <p><strong>Prezzo originale:</strong> € <?php echo number_format($prezzo, 2, ',', '.'); ?></p>
                    <p><strong>Risparmio (<?php echo $sconto; ?>%):</strong> € <?php echo number_format($risparmio, 2, ',', '.'); ?></p>
                    <div class="prezzo-valore">€ <?php echo number_format($prezzo_finale, 2, ',', '.'); ?></div>
                    <div class="fascia">Fascia <?php echo $fascia; ?></div>
                </div>
            <?php endif; ?>
            <a href="index.php" class="link-ricalcola">Calcola di nuovo</a>
        <?php else: ?>
            <form method="post" action="index.php" novalidate>
                <div class="form-group">
                    <label for="prodotto">Prodotto (max 30 caratteri)</label>
                    <input type="text" id="prodotto" name="prodotto" maxlength="30">
                </div>
                <div class="form-group">
                    <label for="prezzo">Prezzo (€)</label>
                    <input type="number" id="prezzo" name="prezzo" step="0.01">
                </div>
                <div class="form-group">
                    <label for="sconto">Sconto (1-90 %)</label>
                    <input type="number" id="sconto" name="sconto">
                </div>
                <button type="submit">Calcola</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>