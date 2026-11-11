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
        <title>Hotte de Noël - Connexion</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>

    <body class="h-full flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-emerald-950 to-red-950">

        <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-2xl border border-amber-500/20 p-8 space-y-6 text-slate-100">

            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-red-600/20 border border-red-500/40 text-red-400 mb-2 shadow-inner">
                    <span class="text-3xl">🎁</span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-200">
                    La Hotte Familiale
                </h1>
                <p class="text-sm text-slate-400">Accédez aux listes de souhaits de la famille</p>
            </div>

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

            <?php if(!isset($_SESSION["auth_code"])) : ?>

                <form method="post" action="api/auth.php" class="space-y-5">
                    <div class="space-y-2">
                        <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                            Numéro de téléphone
                        </label>

                        <div class="relative">
                            <input
                                type="phone"
                                name="phone"
                                required
                                placeholder="06 12 34 56 78"
                                class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all shadow-inner"
                            />
                            <span class="absolute right-3 top-3.5 text-slate-500">📱</span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 rounded-xl font-bold text-slate-950 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-300 active:scale-[0.99] transition-all shadow-lg shadow-amber-500/20 cursor-pointer flex items-center justify-center gap-2"
                    >
                        <span>Recevoir mon code SMS</span>
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </form>

            <?php else: ?>

                <form method="post" action="api/auth.php" class="space-y-5">
                    <div class="space-y-2">
                        <div class="flex justify-between items-center">
                            <label for="code" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                                Code de vérification
                            </label>
                            <span class="text-xs text-amber-400 font-medium">Envoyé par SMS</span>
                        </div>
                        <p class="text-xs text-slate-400">
                            Code envoyé au <strong class="text-slate-200"><?= htmlspecialchars($_SESSION["phoneNumber"] ?? '') ?></strong>
                        </p>

                        <input
                            type="number"
                            name="code"
                            required
                            placeholder="123456"
                            maxlength="6"
                            class="w-full px-4 py-3 text-center text-2xl font-mono tracking-widest rounded-xl bg-slate-800/80 border border-slate-700 text-amber-300 placeholder-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all shadow-inner"
                        />
                    </div>

                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 rounded-xl font-bold text-white bg-gradient-to-r from-red-600 to-emerald-700 hover:from-red-500 hover:to-emerald-600 active:scale-[0.99] transition-all shadow-lg shadow-red-900/40 cursor-pointer"
                    >
                        Valider
                    </button>
                    
                    <div class="text-center pt-2">
                        <a
                            href="api/resend.php"
                            class="text-xs text-slate-400 hover:text-amber-300 underline underline-offset-4 transition-colors"
                        >
                            Vous n'avez pas reçu le code ? Renvoyer
                        </a>
                    </div>
                </form>

            <?php endif ?>
            <div class="pt-4 border-t border-slate-800/60 text-center text-xs text-slate-500">
                🎄 Noël <?= date('Y') ?> • Cercle Familial Privé
            </div>
        </div>
    </body>
</html>