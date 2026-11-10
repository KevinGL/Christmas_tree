<?php

session_start();

if(isset($_SESSION["auth"]))
{
    header("location: home.php");
    die();
}

?>

<!doctype html>
<html lang="fr" class="h-full bg-slate-50">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Authentification</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>

    <body>
        <?php if(isset($_SESSION["flash"])): ?>
            <div>
                <span><?= $_SESSION["flash"]["message"] ?></span>
            </div>
            <?php unset($_SESSION["flash"]) ?>
        <?php endif ?>

        <?php if(!isset($_SESSION["auth_code"])) : ?>

            <form method="post" action="api/auth.php">
                <div>
                    <label>Saisir votre numéro</label>
                    <input type="phone" name="phone" />
                </div>

                <button type="submit">Valider</button>
            </form>

        <?php else: ?>

            <form method="post" action="api/auth.php">
                <div>
                    <label>Saisir le code envoyé à <?= $_SESSION["phoneNumber"] ?></label>
                    <input type="number" name="code" />
                </div>

                <button type="submit">Valider</button>
            </form>

        <?php endif ?>
    </body>
</html>