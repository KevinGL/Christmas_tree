<?php

session_start();

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

require_once __DIR__ . "/api/get_wish_lists.php";
require_once __DIR__ . "/safety/encrypt.php";

$wishLists = getWishLists();
$ownList = getOwnList();
$supportedUsers = getSupportedUsers();
$wishsSupportedUsers = getWishsSupportedUsers();

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
                            Cadeau désiré
                        </th>

                        <th>
                            Pour
                        </th>

                        <th>
                            Pris en charge ?
                        </th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach($wishLists as $w): ?>
                        <tr>
                            <td>
                                <?= $w["wish_name"] ?>
                            </td>

                            <td>
                                <?= $w["username"] ?>
                            </td>

                            <td>
                                <?php if(decryptData($w["wish_supported"]) === "0"): ?>
                                    Non
                                    <a href="api/support.php?id=<?= $w['wish_id'] ?>">Prendre en charge</a>
                                <?php else: ?>
                                    Oui <?php if(decryptData($w["wish_supported"]) == $_SESSION["auth"]) echo "(Par vous)" ?>
                                    <a href="api/unsupport.php?id=<?= $w['wish_id'] ?>">Ne plus prendre en charge</a>
                                <?php endif ?>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <h2>Vos propres souhaits</h2>

            <table>
                <tbody>
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
                </tbody>
            </table>

            <form method="post" action="/api/add_wish.php">
                <div>
                    <label>Cadeau désiré</label>
                    <input name="name" type="text" required />
                </div>

                <input type="submit" value="Ajouter à ma liste" />
            </form>

            <h2>Cadeaux pour personnes que vous prenez en charge</h2>

            <table>
                <tbody>
                    <?php foreach($wishsSupportedUsers as $w): ?>
                        <tr>
                            <td>
                                <?= $w["name"] ?>
                            </td>

                            <td>
                                <a href="edit_wish_supported.php?id=<?= $w['id'] ?>&supported_user=<?= $w['supported_user'] ?>">Modifier</a>
                            </td>

                            <td>
                                <button class="delete-btn" id="<?= $w['supported_user'] ?>_<?= $w['id'] ?>">Supprimer</button>
                            </td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>

            <?php if(count($supportedUsers)): ?>
                <?php foreach($supportedUsers as $su): ?>
                    <form method="post" action="/api/add_wish.php?user_id=<?= $su['id'] ?>">
                        <div>
                            <label>Cadeau pour <?= $su["username"] ?></label>
                            <input name="name" type="text" required />
                        </div>

                        <input type="submit" value="Ajouter à sa liste" />
                    </form>
                <?php endforeach ?>
            <?php endif ?>
        
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
    let userSupported = 0;
    
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
        if(!idDelete.includes("_"))
        {
            window.location = "/api/del_wish.php?id=" + idDelete;
        }

        else
        {
            [supportedUser, idDelete] = idDelete.split('_');
            window.location = "/api/del_wish_supported.php?id=" + idDelete + "&supported_user=" + supportedUser;
        }
    });
</script>