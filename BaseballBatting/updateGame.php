<?php
session_start();
require_once("conn.php");
if (isset($_POST['field_submit'])) {
    $var_playername = $_POST['player_name'];

    $query = "SELECT player_ID, player_name, game_date, player_team FROM common_player_stats_by_game 
                JOIN player USING(player_ID) WHERE player_name = :p_name GROUP BY player_ID ORDER BY game_date";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_name', $var_playername);
        $_SESSION["game_date"]=$_POST["game_date"];
        $_SESSION["plate_appearances"]=$_POST["plate_appearances"];
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
}
function onlyOnePlayer($playerid, $dbo) {
    $var_playerID = $playerid;

    $query = "UPDATE common_player_stats_by_game SET plate_app = :plate_app WHERE player_ID = :p_id AND game_date = :g_date";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID, PDO::PARAM_STR);
        $prepared_stmt->bindValue('g_date', $_SESSION['game_date'], PDO::PARAM_STR);
        $prepared_stmt->bindValue('plate_app', $_SESSION['plate_appearances'], PDO::PARAM_INT);


        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
    output($result, $prepared_stmt);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Baseball Batting</title>
    <link rel="stylesheet" type="text/css" href="style.css?v=<?php echo time(); ?>"/>
</head>
<body>
<div id="navbar">
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="leaderboard.php">Leaderboard</a></li>
        <li><a href="deletePlayer.php">Delete Player</a></li>
        <li><a href="insertGame.php">Insert Game</a></li>
        <li><a href="updateGame.php">Update Game</a></li>
    </ul>
</div>

<h1><b>Baseball Batting Database</b></h1>

<form method="post">
    <fieldset>
        <p>Update game data by player name: </p>
        <div class="input"><label for="id_player">Player Name: </label>
            <input type="text" name="player_name" id="id_player"></div>
        <br/>
        <div class="input"><label for="id_game_date">Game Date: </label>
            <input type="text" name="game_date" id="id_game_date"></div>
        <br/>
        <div class="input"><label for="id_plate_app">Plate Appearances: </label>
            <input type="text" name="plate_appearances" id="id_plate_app"></div>
        <br/>
        <div class="input2"><input type="submit" name="field_submit" value="Submit">
    </fieldset>
</form>
<?php
if (isset($_POST['field_submit'])) {
    if ($result && $prepared_stmt->rowCount() > 1) { ?>
        <form method="post"></form>
        <h3><b>Which player?</b></h3>
        <?php foreach ($result as $row) { ?>
            <form method="post" class="choose">
                <div class="outer">
                    <div class="choose">
                        <h3>Player ID: <?php echo $row["player_ID"]; ?></h3>
                        <h3>Player Name: <?php echo $row["player_name"]; ?></h3>
                        <h3>First Game Played Date: <?php echo $row["game_date"]; ?></h3>
                        <h3>Played With: <?php echo $row["player_team"] ?></h3>
                        <input type="hidden" name="player_id" value="<?php echo $row["player_ID"]; ?>">
                        <input type="submit" name="sub_submit" value="Choose">
                    </div>
                </div>
            </form>

        <?php } ?>
    <?php } else if ($result && $prepared_stmt->rowCount() == 1) {
        foreach ($result as $row) {
            onlyOnePlayer($row["player_ID"], $dbo);
        }
    } else { ?>
        <h3>Sorry, no existing player was found.</h3>
    <?php }
} ?>

<?php
if (isset($_POST['sub_submit'])) {
    $var_playerID = $_POST['player_id'];

    $query = "UPDATE common_player_stats_by_game SET plate_app = :plate_app WHERE player_ID = :p_id AND game_date = :g_date";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID, PDO::PARAM_STR);
        $prepared_stmt->bindValue('g_date', $_SESSION['game_date'], PDO::PARAM_STR);
        $prepared_stmt->bindValue('plate_app', $_SESSION['plate_appearances'], PDO::PARAM_INT);

        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
    output($result, $prepared_stmt);
}

function output($result, $prepared_stmt) {
    if ($result) { ?>
        <div class="results"> Results:
            <h6>Game entry successfully created.</h6>
        </div>
    <?php } else { ?>
        <h3>Sorry, an error occurred.</h3>
    <?php }
} ?>
</body>
</html>
