<?php

session_start();

require_once __DIR__ . "/utils/supported.php";

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

$usersSupported = getSupported();

?>

<!doctype html>
<html lang="fr" class="h-full bg-slate-50">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Christmas Tree - Membres que vous allez combler</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body class="min-h-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-emerald-950 to-red-950 text-slate-100 pb-16">

        <header class="border-b border-amber-500/20 bg-slate-950/60 backdrop-blur-md sticky top-0 z-10 shadow-lg">
            <div class="max-w-5xl mx-auto px-4 py-4 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <span class="text-2xl">🎄</span>
                    <a href="index.php" class="text-xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-200 hover:opacity-80 transition-opacity">
                        Christmas Tree
                    </a>
                </div>

                <div class="flex items-center gap-3">
                    <a href="home.php" class="text-xs font-semibold px-3 py-2 rounded-xl bg-slate-900 border border-slate-700/50 text-slate-300 hover:bg-slate-800 transition-all shadow-sm">
                        Accueil
                    </a>
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

            <section class="space-y-6">
                <div class="flex items-center justify-between">
                    <h2 class="text-xl font-bold text-amber-600 flex items-center gap-2">
                        <span>🎁</span> Liste des membres de la famille
                    </h2>
                    <span class="text-xs text-slate-400 bg-slate-900/80 px-3 py-1.5 rounded-full border border-slate-800">
                        <?= count($usersSupported) ?> membres au total
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <?php foreach($usersSupported as $u): ?>
                        <div class="p-5 rounded-2xl bg-slate-900/70 border <?= $u["supported"] ? 'border-emerald-500/40 bg-emerald-950/20' : 'border-slate-800' ?> backdrop-blur-sm shadow-xl flex items-center justify-between transition-all hover:border-amber-500/40">
                            
                            <!-- Info utilisateur -->
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-slate-800 to-slate-950 border border-slate-700/50 flex items-center justify-center text-xl shadow-inner">
                                    👤
                                </div>
                                <div>
                                    <h3 class="font-semibold text-slate-100 text-lg tracking-wide">
                                        <?= htmlspecialchars($u["username"]) ?>
                                    </h3>
                                    <p class="text-xs text-slate-400 mt-0.5">
                                        <?php if($u["supported"]): ?>
                                            <span class="text-emerald-400 font-medium">✨ Cadeau déjà programmé</span>
                                        <?php else: ?>
                                            <span class="text-slate-500">Aucun cadeau attribué pour l'instant</span>
                                        <?php endif ?>
                                    </p>
                                </div>
                            </div>

                            <!-- Badge de statut -->
                            <div>
                                <?php if($u["supported"]): ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm" title="Pris en charge">
                                        &#x2713;
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-800 text-slate-500 border border-slate-700/50" title="En attente">
                                        &bull;
                                    </span>
                                <?php endif ?>
                            </div>

                        </div>
                    <?php endforeach ?>
                </div>
            </section>
        
        </main>

        <footer class="text-center text-xs text-slate-500 mt-12 space-y-1">
            <p>🎄 Joyeuses Fêtes et Bonnes Préparations de Noël ! 🎄</p>
            <p>Christmas Tree © <?= date('Y') ?></p>
        </footer>
        
    </body>
</html>