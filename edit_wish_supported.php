<?php

session_start();

require_once __DIR__ . "/db/pdo.php";

if(!isset($_SESSION["auth"]))
{
    header("location: login.php");
    die();
}

if(!isset($_GET["id"]))
{
    header("location: home.php");
    die();
}

if(!isset($_GET["supported_user"]))
{
    header("location: home.php");
    die();
}

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM supported_users WHERE user_id = :user_id AND supported_by = :supported_by");
$stmt->execute([
    "user_id" => $_GET["supported_user"],
    "supported_by" => $_SESSION["auth"]
]);

if(!$stmt->fetch())
{
    header("location: home.php");
    die();
}

$stmt = $pdo->prepare("SELECT * FROM wish_lists WHERE id = :id AND user_id = :user_id");
$stmt->execute([
        "id" => $_GET["id"],
        "user_id" => $_GET["supported_user"]
    ]);

$wish = $stmt->fetch();

if(!$wish)
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
        <title>Christmas Tree - Modifier le cadeau pris en charge</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>

    <body class="h-full flex items-center justify-center p-4 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-800 via-emerald-950 to-red-950">
        <div class="w-full max-w-md bg-slate-900/90 backdrop-blur-md rounded-2xl shadow-2xl border border-amber-500/20 p-8 space-y-6 text-slate-100">

            <div class="text-center space-y-2">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 mb-1 shadow-inner">
                    <span class="text-3xl">🎁</span>
                </div>
                <h1 class="text-2xl font-extrabold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-amber-200 via-amber-400 to-amber-200">
                    Modifier le cadeau
                </h1>
                <p class="text-sm text-slate-400">Mettez à jour l'idée cadeau pour cette personne</p>
            </div>
        
            <form method="post" action="api/edit_wish_supported.php" class="space-y-5">
                <input name="id" value="<?= $_GET['id'] ?>" hidden />
                <input name="supported_user" value="<?= $_GET['supported_user'] ?>" hidden />
                <div class="space-y-2">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Cadeau / Souhait
                    </label>
                    <input
                        name="name"
                        type="text"
                        required
                        value="<?= $wish['name'] ?>"
                        class="w-full px-4 py-3 rounded-xl bg-slate-800/80 border border-slate-700 text-slate-100 placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent transition-all shadow-inner text-sm"
                    />
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <button
                        type="submit"
                        class="w-full py-3.5 px-4 rounded-xl font-bold text-slate-950 bg-gradient-to-r from-amber-400 via-amber-300 to-amber-400 hover:from-amber-300 hover:to-amber-300 active:scale-[0.99] transition-all shadow-lg shadow-amber-500/20 cursor-pointer"
                    >
                        Mettre à jour
                    </button>

                    <a 
                        href="home.php" 
                        class="w-full py-3 px-4 rounded-xl font-semibold text-xs uppercase tracking-wider text-center text-slate-400 bg-slate-800/60 hover:bg-slate-800 hover:text-slate-200 border border-slate-700/60 transition-all"
                    >
                        Annuler
                    </a>
                </div>
            </form>

            <div class="pt-4 border-t border-slate-800/60 text-center text-xs text-slate-500">
                🎄 Christmas Tree • Cercle Familial
            </div>

        </div>
    </body>
</html>