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
        <title>Christmas Tree - Accueil</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body class="min-h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-emerald-950 to-red-950 text-slate-100 pb-16">

        <header class="border-b border-amber-500/20 bg-slate-950/60 backdrop-blur-md sticky top-0 z-10 shadow-lg">
            <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎄</span>
                    <h1 class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-200">
                        Christmas Tree
                    </h1>
                </div>
                <div>
                    <a href="logout.php" class="text-xs font-semibold px-4 py-2 rounded-xl bg-red-950/80 border border-red-500/40 text-red-300 hover:bg-red-900/80 transition-all shadow-sm">
                        Déconnexion
                    </a>
                </div>
            </div>
        </header>

        <main class="max-w-5xl mx-auto px-4 py-8 space-y-10">

            <?php if(isset($_SESSION["flash"])): ?>
                <div
                    x-data="{ show: true }" 
                    x-show="show"
                    class="p-4 rounded-xl bg-red-950/80 border border-red-500/40 text-red-200 text-sm flex items-start gap-3 shadow-lg"
                >
                    <span class="text-base"> <?= $_SESSION["flash"]["status"] === "error" ? "⚠️" : "🎄"?></span>
                    <span class="flex-1"><?= htmlspecialchars($_SESSION["flash"]["message"]) ?></span>

                    <button 
                        @click="show = false" 
                        class="text-slate-400 hover:text-slate-100 transition-colors cursor-pointer text-lg leading-none px-1"
                        title="Fermer"
                    >
                        &times;
                    </button>
                </div>
                <?php unset($_SESSION["flash"]) ?>
            <?php endif ?>

            <section class="bg-slate-900/80 backdrop-blur-md rounded-2xl shadow-xl border border-amber-500/20 p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-800 pb-4">
                    <span class="text-xl">🎁</span>
                    <h2 class="text-lg font-bold text-amber-300">Souhaits des autres membres</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-800 text-xs font-semibold uppercase tracking-wider text-slate-400">
                                <th class="py-3 px-4">Cadeau désiré</th>
                                <th class="py-3 px-4">Pour</th>
                                <th class="py-3 px-4 text-right">Pris en charge ?</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-800/60 text-sm">
                            <?php if(empty($wishLists)): ?>
                                <tr>
                                    <td colspan="3" class="py-6 text-center text-slate-500">Aucun souhait trouvé pour le moment.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach($wishLists as $w): ?>
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="py-3.5 px-4 font-medium text-slate-200"><?= htmlspecialchars($w["wish_name"]) ?></td>
                                        <td class="py-3.5 px-4 text-slate-300"><?= htmlspecialchars($w["username"]) ?></td>
                                        <td class="py-3.5 px-4 text-right">
                                            <?php if(decryptData($w["wish_supported"]) === "0"): ?>
                                                <div class="inline-flex items-center gap-2">
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-slate-800 text-slate-300 border border-slate-700">Non</span>
                                                    <a
                                                        href="api/support.php?id=<?= $w['wish_id'] ?>"
                                                        class="px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition-all shadow-sm"
                                                    >
                                                        Prendre en charge
                                                    </a>
                                                </div>

                                            <?php else: ?>
                                                <div class="inline-flex items-center gap-2 flex-wrap justify-end">
                                                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-red-950/80 text-red-300 border border-red-500/30">
                                                        Oui <?php if(decryptData($w["wish_supported"]) == $_SESSION["auth"]) echo "(Par vous)" ?>
                                                    </span>
                                                    <a
                                                        href="api/unsupport.php?id=<?= $w['wish_id'] ?>"
                                                        class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-all"
                                                    >
                                                        Annuler
                                                    </a>
                                                </div>
                                            <?php endif ?>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                <section class="bg-slate-900/80 backdrop-blur-md rounded-2xl shadow-xl border border-amber-500/20 p-6 space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-4 mb-4">
                            <span class="text-xl">⭐</span>
                            <h2 class="text-lg font-bold text-amber-300">Vos propres souhaits</h2>
                        </div>

                        <?php if(empty($ownList)): ?>
                            <p class="text-sm text-slate-500 py-4 text-center">Votre liste est vide pour l'instant.</p>
                        <?php else: ?>
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                <?php foreach($ownList as $w): ?>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                        <span class="text-sm font-medium text-slate-200"><?= htmlspecialchars($w["name"]) ?></span>

                                        <div class="flex items-center gap-2">

                                            <a href="edit_wish.php?id=<?= $w['id'] ?>" class="text-xs px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 transition-colors">Modifier</a>

                                            <button class="delete-btn text-xs px-2.5 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 border border-red-500/30 transition-colors" id="<?= $w['id'] ?>">Supprimer</button>

                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>

                    <form method="post" action="/api/add_wish.php" class="space-y-3 pt-4 border-t border-slate-800">
                        <div>
                            <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400 mb-1">Cadeau désiré</label>
                            <input name="name" type="text" required placeholder="Ex: Un pull de Noël moche" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-100 text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all" />
                        </div>

                        <input type="submit" value="Ajouter à ma liste" class="w-full py-2.5 px-4 rounded-xl font-bold text-xs uppercase tracking-wider text-slate-950 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-300 transition-all shadow-md cursor-pointer" />
                    </form>
                </section>

                <section class="bg-slate-900/80 backdrop-blur-md rounded-2xl shadow-xl border border-amber-500/20 p-6 space-y-4 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 border-b border-slate-800 pb-4 mb-4">
                            <span class="text-xl">🛍️</span>
                            <h2 class="text-lg font-bold text-amber-300">Cadeaux à offrir (Pris en charge)</h2>
                        </div>

                        <?php if(empty($wishsSupportedUsers)): ?>
                            <p class="text-sm text-slate-500 py-4 text-center">Vous ne gérez aucune personne</p>
                        <?php else: ?>
                            <div class="space-y-2 max-h-60 overflow-y-auto pr-1">
                                <?php foreach($wishsSupportedUsers as $w): ?>
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-800/50 border border-slate-700/50">
                                        <span class="text-sm font-medium text-slate-200"><?= htmlspecialchars($w["name"]) ?></span>
                                        <div class="flex items-center gap-2">
                                            <a
                                                href="edit_wish_supported.php?id=<?= $w['id'] ?>&supported_user=<?= $w['supported_user'] ?>"
                                                class="text-xs px-2.5 py-1 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 transition-colors"
                                            >
                                                Modifier
                                            </a>

                                            <button class="delete-btn text-xs px-2.5 py-1 rounded-lg bg-red-950/80 hover:bg-red-900 text-red-300 border border-red-500/30 transition-colors" id="<?= $w['supported_user'] ?>_<?= $w['id'] ?>">Supprimer</button>
                                        </div>
                                    </div>
                                <?php endforeach ?>
                            </div>
                        <?php endif ?>
                    </div>
                    
                    <?php if(count($supportedUsers)): ?>
                        <?php foreach($supportedUsers as $su): ?>
                            <div class="space-y-3 pt-4 border-t border-slate-800">
                                <form method="post" action="/api/add_wish.php?user_id=<?= $su['id'] ?>" class="space-y-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-400">Cadeau pour <?= htmlspecialchars($su["username"]) ?></label>
                                    <div class="flex gap-2">
                                        <input name="name" type="text" required placeholder="Idée cadeau..." class="flex-1 px-3.5 py-2 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-100 text-sm placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-400 transition-all" />
                                        <input type="submit" value="Ajouter" class="px-4 py-2 rounded-xl font-bold text-xs text-slate-950 bg-amber-400 hover:bg-amber-300 transition-all cursor-pointer shadow-sm" />
                                    </div>
                                </form>
                            </div>
                        <?php endforeach ?>
                    <?php endif ?>

                </section>
            </div>
        
        </main>

        <div id="modal" style="visibility: hidden" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm transition-all">
            <div class="bg-slate-900 border border-amber-500/30 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl space-y-4 text-center">
                <div class="w-12 h-12 rounded-full bg-red-600/20 border border-red-500/40 text-red-400 flex items-center justify-center mx-auto text-xl">
                    ⚠️
                </div>
                <h3 class="text-lg font-bold text-slate-100">Confirmation</h3>
                <p class="text-sm text-slate-400">Êtes-vous sûr de vouloir supprimer ce souhait ?</p>

                <div class="flex gap-3 pt-2">
                    <button id="no" class="flex-1 py-2.5 rounded-xl font-semibold text-sm bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-all cursor-pointer">
                        Non
                    </button>
                    <button id="yes" class="flex-1 py-2.5 rounded-xl font-semibold text-sm bg-red-600 hover:bg-red-500 text-white transition-all shadow-lg shadow-red-900/30 cursor-pointer">
                        Oui, supprimer
                    </button>
                </div>
            </div>
        </div>

        <footer class="text-center text-xs text-slate-500 mt-12 space-y-1">
            <p>🎄 Joyeuses Fêtes et Bonnes Préparations de Noël ! 🎄</p>
            <p>Christmas Tree © <?= date('Y') ?></p>
        </footer>
        
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