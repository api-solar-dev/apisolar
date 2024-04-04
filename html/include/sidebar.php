<div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
            <div class="nav">
                <div class="sb-sidenav-menu-heading">Pages</div>
                <?php if (!isset($_SESSION['ID_User'])) { ?>
                    <a class="nav-link collapsed" data-bs-toggle="collapse" data-bs-target="#collapseCompte" aria-expanded="false" aria-controls="collapseCompte">
                        <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                        Gestion de comptes
                        <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                    </a>

                    <div class="collapse" id="collapseCompte" aria-labelledby="headingOne">
                        <nav class="sb-sidenav-menu-nested nav">
                            <a class="nav-link" href="connexion">Connexion</a>
                            <a class="nav-link" href="inscription">Inscription</a>
                        </nav>
                    </div>
                <?php } ?>
                <?php if (isset($_SESSION['ID_User'])) { ?>
                    <a class="nav-link" href="./">
                        <div class="sb-nav-link-icon"><i class="fas fa-solid fa-house"></i></div> Lancer le simulateur
                    </a>

                <?php } ?>

            </div>
        </div>
        <?php if (isset($_SESSION['ID_User'])) { ?>
            <div class="sb-sidenav-footer">
                <div class="small">Connecté en tant que :</div>
                <?php echo $_SESSION['Prenom'] . ' ' . $_SESSION['Nom'] ?>
            </div>
        <?php } ?>
    </nav>
</div>