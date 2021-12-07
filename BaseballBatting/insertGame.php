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
        $_SESSION["player_team"]=$_POST["player_team"];
        $_SESSION["opp_team"]=$_POST["opp_team"];
        $_SESSION["points_scored"]=$_POST["points_scored"];
        $_SESSION["opp_points_scored"]=$_POST["opp_points_scored"];
        $_SESSION["game_result"]=$_POST["game_result"];
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
}
function onlyOnePlayer($playerid, $dbo) {
    $var_playerID = $playerid;

    $query = "CALL create_entry(:p_id, :g_date, :p_team, :o_team, :p_score, :o_score, :g_result)";
    // CALL create_entry(:p_id, :g_date, :p_team, :o_team, :p_score, :o_score, :g_result

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID, PDO::PARAM_STR);
        $prepared_stmt->bindValue('g_date', $_SESSION['game_date'], PDO::PARAM_STR);
        $prepared_stmt->bindValue('p_team', $_SESSION['player_team'], PDO::PARAM_STR);
        $prepared_stmt->bindValue('o_team', $_SESSION['opp_team'], PDO::PARAM_STR);
        $prepared_stmt->bindValue('p_score', $_SESSION['points_scored'], PDO::PARAM_INT);
        $prepared_stmt->bindValue('o_score', $_SESSION['opp_points_scored'], PDO::PARAM_INT);
        $prepared_stmt->bindValue('g_result', $_SESSION['game_result'], PDO::PARAM_STR);

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
        <p>Create a new game entry: </p>
        <div class="input"><label for="id_player">Player Name: </label>
            <input type="text" name="player_name" id="id_player_name"></div><br />
        <div class="input"><label for="id_game_date">Game Date (yyyy-mm-dd): </label>
            <input type="text" name="game_date" id="id_game_date"></div><br />
        <div class="input"><label for="id_player_team">Player Team: </label>
            <input type="text" name="player_team" id="id_player_team" maxlength="3"></div><br />
        <div class="input"><label for="id_opp_team">Opposing Team: </label>
            <input type="text" name="opp_team" id="id_opp_team" maxlength="3"></div><br />
        <div class="input"><label for="id_points_scored">Points Scored: </label>
            <input type="text" name="points_scored" id="id_points_scored"></div><br />
        <div class="input"><label for="id_opp_points_scored">Opposing Points Scored: </label>
            <input type="text" name="opp_points_scored" id="id_opp_points_scored"></div><br />
        <div class="input"><label for="id_game_result">Final Result (W or L): </label>
            <input type="text" name="game_result" id="id_game_result"></div><br />

        <div class="input2"><input type="submit" name="field_submit" value="Submit"></div>
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

    $query = "CALL create_entry(:p_id, :g_date, :p_team, :o_team, :p_score, :o_score, :g_result)";
    // CALL create_entry(:p_id, :g_date, :p_team, :o_team, :p_score, :o_score, :g_result

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID, PDO::PARAM_STR);
        $prepared_stmt->bindValue('g_date', $_SESSION["game_date"], PDO::PARAM_STR);
        $prepared_stmt->bindValue('p_team', $_SESSION["player_team"], PDO::PARAM_STR);
        $prepared_stmt->bindValue('o_team', $_SESSION["opp_team"], PDO::PARAM_STR);
        $prepared_stmt->bindValue('p_score', $_SESSION["points_scored"], PDO::PARAM_INT);
        $prepared_stmt->bindValue('o_score', $_SESSION["opp_points_scored"], PDO::PARAM_INT);
        $prepared_stmt->bindValue('g_result', $_SESSION["game_result"], PDO::PARAM_STR);

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
