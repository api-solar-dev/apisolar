<?php
session_start();

require_once 'include/connexion_bdd.php';
// Gestion des messages en fonction des paramètres GET
if (isset($_GET['disconnect']) && !empty($_GET['disconnect']) && !isset($_SESSION['ID_User'])) {
    $message_warning = "Vous avez été déconnecté !";
}

if (isset($_GET['connect']) && !empty($_GET['connect']) && !isset($_SESSION['ID_User'])) {
    $message_info = "Vous devez être connecté !";
}

if (isset($_POST['submit'])) {
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['passwordhash']);
    if (!empty($email) && isset($email) || !empty($mdp) && isset($mdp)) {
        try {
            $req_verif_existe_user = $dbh->prepare("SELECT * FROM users WHERE Email = :email");
            $req_verif_existe_user->bindParam(':email', $email); // Binding parameter
            $req_verif_existe_user->execute();
            $resultat_verif_existe_user = $req_verif_existe_user->rowCount();
        } catch (PDOException $e) {
            echo "Erreur!: " . $e->getMessage() . "<br/>";
            die();
        }
        if ($resultat_verif_existe_user > 0) {
            $resultat_user = $req_verif_existe_user->fetch();

            if ($resultat_user['Mdp'] === $mdp) {
                // Démarrage de la session et enregistrement des informations de l'utilisateur
                $_SESSION['ID_User'] = $resultat_user['ID_User'];
                $_SESSION['Prenom'] = $resultat_user['Prenom'];
                $_SESSION['Nom'] = $resultat_user['Nom'];
                $_SESSION['Email'] = $resultat_user['Email'];
                $_SESSION['Adresse'] = $resultat_user['Adresse'];
                $_SESSION['Conso'] = $resultat_user['Conso'];
                $_SESSION['CodePostal'] = $resultat_user['CodePostal'];
                $_SESSION['Ville'] = $resultat_user['Ville'];

                $success = 'Bienvenue ' . $resultat_user['Prenom'] . ' ' . $resultat_user['Nom'] . ' !';
                header('Location: ./');
            } else {
                $erreur = "Email ou mot de passe incorrect";
            }
        } else {
            $erreur = "L'utilisateur n'existe pas !";
        }
    } else {
        $erreur = "Tous les champs doivent être complétés";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Connexion</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
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
                    <h1 class="mt-4">Connexion</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item active">Connexion</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Connexion à votre compte</h3>
                            </div>
                            <div class="card-body">

                                <?php if (isset($message_warning) && !isset($message_info) && !isset($erreur)) { ?>
                                    <div class="alert alert-danger mb-3" role="alert"><?php echo $message_warning ?></div>
                                <?php } ?>
                                <?php if (isset($message_info) && !isset($message_warning) && !isset($erreur)) { ?>
                                    <div class="alert alert-info mb-3" role="info"><?php echo $message_info ?></div>
                                <?php } ?>
                                <?php if (isset($erreur)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <form method="post">
                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" type="email" placeholder="prenom.nom@gmail.com" required />
                                                <label>Adresse Email</label>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="mdp" id="password" type="password" placeholder="Mot de passe" required />
                                                <label>Mot de passe</label>
                                            </div>
                                        </div>
                                        <input type="hidden" id="passwordhash" name="passwordhash">
                                        <div class="mt-4 mb-0">
                                            <input type="submit" name="submit" class="btn btn-primary" value="Connexion">
                                        </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
            <?php
            require_once 'include/footer.php';
            ?>
        </div>
    </div>
    <script src="js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-md5"></script>
    <script>
        $(document).ready(function() {
            var salt = 'vivelescornichons';
            $('#password').on('input', function() {
                var password = $(this).val();
                var hashPass = md5(password + salt);
                $('#passwordhash').val(hashPass);
            })
        });
    </script>
</body>

</html>
<script>
    // Éviter le renvoi des données lorsque la page est rafraîchie
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>
