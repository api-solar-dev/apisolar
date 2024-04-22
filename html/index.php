<?php
session_start();
require_once 'include/connexion_bdd.php';
require_once 'include/verif_user_connect.php';

$erreur = '';

if (isset($_SESSION['ID_User'])) {
    $conso_par_an = $_SESSION['Conso'];
    $adresse = $_SESSION['Adresse'];
    $codepostal = $_SESSION['CodePostal'];
    $ville = $_SESSION['Ville'];
}

if (isset($_POST['submit'])) {
    if (!empty($adresse) && isset($adresse)) {
        $address_format = urlencode($adresse);
        $codepostal_format = urlencode($codepostal);
        $ville_format = urlencode($ville);

        $url = "https://nominatim.openstreetmap.org/search?street=$address_format&postalcode=$codepostal_format&city=$ville_format&format=json&limit=1";

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
        ]);

        $curl_nominatim = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            $erreur = "Erreur lors de la requête vers le service de géolocalisation.";
        } else {
            $resp_nominatim = json_decode($curl_nominatim, true);
            if ((!empty($resp_nominatim))) {
                if (!empty($resp_nominatim['code'])) {
                    $erreur = "Aucune adresse trouvée.";
                } else {
                    $longitude = $resp_nominatim[0]['lon'];
                    $latitude = $resp_nominatim[0]['lat'];

                    $url = "https://solar.googleapis.com/v1/buildingInsights:findClosest?location.latitude=$latitude&location.longitude=$longitude&requiredQuality=HIGH&key=AIzaSyCw2TwxtHdPfxbp-bFQp6NZZIC98Xp9PsM";

                    $curl = curl_init();
                    curl_setopt_array($curl, [
                        CURLOPT_URL => $url,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_FOLLOWLOCATION => true,
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                    ]);

                    $curl_googlesolar = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    if ($err) {
                        $erreur = "Erreur lors de la requête vers l'API Google Solar.";
                    } else {
                        $resp_googlesolar = json_decode($curl_googlesolar, true);
                        if ((!empty($resp_googlesolar))) {
                            if (!empty($resp_googlesolar['error'])) {
                                $erreur = "Désolé, le projet Sunroof n'a pas encore atteint cette adresse.";
                            }
                        } else {
                            $erreur = "Aucune donnée retournée par l'API Google Solar.";
                        }
                    }
                }
            } else {
                $erreur = "Aucune adresse trouvée.";
            }
        }
    } else {
        $erreur = "Les champs obligatoires doivent être remplis.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Simulateur</title>
    <link href="css/table.css" rel="stylesheet" />
    <link href="css/styles.css" rel="stylesheet" />
    <script src="js/all.js" crossorigin="anonymous"></script>
</head>

<body>
    <?php
    require_once 'include/navigation.php';
    ?>
    <div id="layoutSidenav">
        <?php
        require_once 'include/sidebar.php';
        ?>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Simulateur</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item"><a href="./">Simulateur</a></li>
                    </ol>
                    <!-- Affichage d'un message d'erreur s'il y a une erreur -->
                    <?php
                    if (!empty($erreur)) {
                    ?>
                        <div class="alert alert-danger mb-3" role="alert"><?php echo $erreur ?></div>
                    <?php
                    }
                    ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="post">
                                <div class="form-group">
                                    <label for="disabledTextInput">Adresse</label>
                                    <input type="text" id="disabledTextInput" class="form-control" placeholder="<?php echo $adresse . ' ' . $codepostal . ' ' . $ville ?>" disabled>
                                </div>
                                <div class="form-group">
                                    <label for="disabledTextInput">Consommation Annuelle</label>
                                    <input type="text" id="disabledTextInput" class="form-control" placeholder="<?php echo $conso_par_an; ?>" disabled>
                                </div>
                                <button type="submit" name="submit" class="btn btn-primary mt-3">Lancer le simulateur</button>
                            </form>
                        </div>
                    </div>
                    <?php if (isset($resp_googlesolar)) {
                        foreach ($resp_googlesolar['solarPotential']['solarPanelConfigs'] as $resp_googlesolar2) {
                            if ($resp_googlesolar['solarPotential']['maxArrayPanelsCount'] == $resp_googlesolar2['panelsCount']) {
                                $totalElectricityProduction = $resp_googlesolar2['yearlyEnergyDcKwh'];
                            }
                        }
                        $economie_electricite = $totalElectricityProduction - $conso_par_an;
                        $totalElectricityProduction_euros = $totalElectricityProduction * 0.2516;
                        $economie_electricite_euros = $economie_electricite * 0.2516;
                    ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>Économie d'électricité estimée en utilisant le nombre maximal de panneaux solaires soit <?php echo $resp_googlesolar['solarPotential']['maxArrayPanelsCount'] ?> :</h5>
                                <p><?php echo 'Tu généreras en électricité ' . round($totalElectricityProduction, 2) . ' kWh/an soit ' . round($totalElectricityProduction_euros, 2) . ' €/an'; ?></p>
                                <p><?php if ($economie_electricite > 0) {
                                        echo 'Tu gagneras ' . round($economie_electricite, 2) . ' kWh/an soit ' . round($economie_electricite_euros, 2) . ' €/an';
                                    } else {
                                        echo 'Tu perdras ' . round($economie_electricite, 2) . ' kWh/an soit ' . round($economie_electricite_euros, 2) . ' €/an';
                                    } ?></p>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </main>
            <?php
            require_once 'include/footer.php';
            ?>
        </div>
    </div>
    <script>
        // Éviter le renvoi des données lorsque la page est rafraîchie
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
</body>

</html>
