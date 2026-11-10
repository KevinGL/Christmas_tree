<?php

session_start();

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

require_once __DIR__ . "/api/get_wish_lists.php";

$wishLists = getWishLists();
$ownList = getOwnList();

?>

<!doctype html>
<html lang="fr" class="h-full bg-slate-50">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Page d'accueil</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>

    <body>
        <?php if(isset($_SESSION["flash"])): ?>
            <div>
                <span><?= $_SESSION["flash"]["message"] ?></span>
            </div>
            <?php unset($_SESSION["flash"]) ?>
        <?php endif ?>

        <main>
            <h2>Souhaits des autres membres</h2>
            
            <table>
                <thead>
                    <tr>
                        <th>
                            Membre de la famille
                        </th>

                        <th>
                            Liste de cadeaux souhaités
                        </th>

                        <th>
                            Pris en charge ?
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($wishLists as $key => $value): ?>
                        <tr>
                            <td>
                                <?= $key ?>
                            </td>

                            <td>
                                <ul>
                                    <?php foreach($value as $v): ?>
                                        <li><?= $v ?></li>
                                    <?php endforeach ?>
                                </ul>
                            </td>

                            <td>
                                Non
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <h2>Vos propres souhaits</h2>

            <table>
                <body>
                    <?php foreach($ownList as $w): ?>
                        <tr>
                            <td>
                                <?= $w["name"] ?>
                            </td>

                            <td>
                                <a href="edit_wish.php?id=<?= $w['id'] ?>">Modifier</a>
                            </td>

                            <td>
                                <button class="delete-btn" id="<?= $w['id'] ?>">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </body>
            </table>

            <form method="post" action="/api/add_wish.php">
                <div>
                    <label>Cadeau désiré</label>
                    <input name="name" type="text" required />
                </div>

                <input type="submit" />
            </form>
        
        </main>

        <div id="modal" style="visibility: hidden">
            <div>Sûr de vouloir supprimer ce souhait ?</div>
            <div>
                <span id="yes">Oui</span>
                <span id="no">Non</span>
            </div>
        </div>
        
    </body>
</html>

<script>
    const deleteButtons = document.querySelectorAll(".delete-btn");
    const modal = document.getElementById("modal");
    const btnNo = document.getElementById("no");
    const btnYes = document.getElementById("yes");
    let idDelete = 0;
    
    deleteButtons.forEach(button =>
    {
        button.addEventListener("click", () =>
        {
            modal.style.visibility = "visible";
            idDelete = button.id;
        });
    });

    btnNo.addEventListener("click", () =>
    {
        modal.style.visibility = "hidden";
    });

    btnYes.addEventListener("click", () =>
    {
        window.location = "/api/del_wish.php?id=" + idDelete;
    });
</script>