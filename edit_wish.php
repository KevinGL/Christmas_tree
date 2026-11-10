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

$pdo = getPDO();

$stmt = $pdo->prepare("SELECT * FROM wish_lists WHERE id = :id AND user_id = :user_id");
$stmt->execute([
        "id" => $_GET["id"],
        "user_id" => $_SESSION["auth"]
    ]);

$wish = $stmt->fetch();

if(!$wish)
{
    header("location: home.php");
    die();
}

?>

<?php if($_SERVER["REQUEST_METHOD"] === "GET"): ?>

<!doctype html>
<html lang="fr" class="h-full bg-slate-50">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Modifiez votre souhait</title>
        <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    </head>

    <body>
        <form method="post">
            <div>
                <label>Souhait</label>
                <input name="name" type="text" value="<?= $wish['name'] ?>" />
            </div>

            <button type="submit">Mettre à jour</button>
        </form>        
    </body>
</html>

<?php else:

$stmt = $pdo->prepare("UPDATE wish_lists SET name = :name WHERE id = :id");
$stmt->execute([
        "name" => $_POST["name"],
        "id" => $_GET["id"]
    ]);

$_SESSION["flash"] = ["status" => "success", "message" => "Souhait mis à jour"];
header("location: home.php");

endif ?>