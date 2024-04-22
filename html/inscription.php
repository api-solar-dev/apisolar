<?php

session_start();
require_once 'include/connexion_bdd.php';

if (isset($_POST['submit'])) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $adresse = htmlspecialchars($_POST['adresse']);
    $codepostal = htmlspecialchars($_POST['codepostal']);
    $ville = htmlspecialchars($_POST['ville']);
    $conso = htmlspecialchars($_POST['conso']);
    $email = htmlspecialchars($_POST['email']);
    $mdp = htmlspecialchars($_POST['passwordhash']);
    $vemail = '@';
    $vespace  = ' ';
    $espace = strpos($prenom, $vespace);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($espace === false) {
            if (!empty($nom) && isset($nom) || !empty($prenom) && isset($prenom) ||
             !empty($email) && isset($email) || !empty($mdp) && isset($mdp)) {
                try {
                    $mailexist = $dbh->prepare('SELECT ID_User FROM users WHERE Email = :email');
                    $mailexist->bindParam(':email', $email); // Binding parameter
                    $mailexist->execute();
                } catch (PDOException $e) {
                    echo "Erreur!: " . $e->getMessage() . "<br/>";
                    die();
                }
                if ($mailexist->rowCount() == 0) {
                    try {
                        $insertnewuser = $dbh->prepare('INSERT INTO
                        users(Prenom,Nom,Email,Adresse,CodePostal,Ville,Conso,Mdp)
                        VALUES (?,?,?,?,?,?,?,?)');
                        $insertnewuser->bindParam(1, $prenom);
                        $insertnewuser->bindParam(2, $nom);
                        $insertnewuser->bindParam(3, $email);
                        $insertnewuser->bindParam(4, $adresse);
                        $insertnewuser->bindParam(5, $codepostal);
                        $insertnewuser->bindParam(6, $ville);
                        $insertnewuser->bindParam(7, $conso);
                        $insertnewuser->bindParam(8, $mdp);
                        $insertnewuser->execute();
                        header('Location: ./connexion');
                    } catch (PDOException $e) {
                        echo "Erreur!: " . $e->getMessage() . "<br/>";
                        die();
                    }
                    $success = 'L\'utilisateur ' . $prenom . ' ' . $nom . ' à bien été crée';
                } else {
                    $erreur = "L'adresse mail est déja utilisé";
                }
            } else {
                $erreur = "Tous les champs doivent être complétés";
            }
        } else {
            $erreur = "Le champ prénom ne doit pas contenir d'espaces";
        }
    } else {
        $erreur = "Adresse mail invalide";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title>Ajouter une utilisateur</title>
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
                    <h1 class="mt-4">Ajouter une utilisateur</h1>
                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item">Pages</li>
                        <li class="breadcrumb-item active">Ajouter une utilisateur</li>
                    </ol>
                    <div class="row justify-content-center">
                        <div class="col-lg-7">
                            <div class="card-header">
                                <h3 class="text-center font-weight-light my-4">Ajouter une utilisateur</h3>
                            </div>
                            <div class="card-body">
                                <?php
                                if (isset($erreur) && !isset($success)) { ?>
                                    <div class="alert alert-danger" role="alert">
                                        <?php echo $erreur ?>
                                    </div>
                                <?php }
                                ?>
                                <?php
                                if (isset($success) && !isset($erreur)) { ?>
                                    <div class="alert alert-success" role="success">
                                        <?php echo $success ?>
                                    </div>
                                <?php }
                                ?>
                                <div class="card">
                                    <div class="card-body">
                                        <form method="post">
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" type="text" name="prenom" placeholder="Entrer votre prénom" required />
                                                        <label>Prénom</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input class="form-control" type="text" name="nom" placeholder="Entrer votre nom" required />
                                                        <label>Nom</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="email" type="email" placeholder="prenom.nom@gmail.com" required />
                                                <label>Adresse Email</label>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" type="text" name="adresse" placeholder="Entrer votre adresse" required />
                                                        <label>Adresse</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating mb-3 mb-md-0">
                                                        <input class="form-control" type="text" name="codepostal" placeholder="Entrer votre code postal" required />
                                                        <label>Code Postal</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input class="form-control" type="text" name="ville" placeholder="Entrer votre ville" required />
                                                        <label>Ville</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input class="form-control" type="text" name="conso" placeholder="Entrer votre consommation electrique/an en kWh" required />
                                                        <label>Consommation electrique/an en kWh</label>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-floating mb-3">
                                                <input class="form-control" name="mdp" id="password" type="password" placeholder="Mot de passe" required />
                                                <label>Mot de passe</label>
                                            </div>
                                            <input type="hidden" id="passwordhash" name="passwordhash">
                                            <div class="mt-4 mb-0">
                                                <input type="submit" name="submit" class="btn btn-primary" value="Enregistrer">
                                            </div>
                                        </form>
                                    </div>
                                </div>
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
        // Éviter le renvoi des données lorsque la page est rafraîchie
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
    <script>
        $(document).ready(function() {
            var hashPass = "";
            var salt = 'vivelescornichons';
            console.log(salt);
            $('#password').on('input', function() {
                var password = $(this).val();
                var hashPass = md5(password + salt);
                $('#passwordhash').val(hashPass);
                $('#salt').val(salt);
            })
        });
    </script>
</body>


</html>