<?php
require_once("conn.php");
if (isset($_POST['field_submit'])) {
    $var_playername = $_POST['player_name'];

    $query = "SELECT player_ID, player_name, game_date, player_team FROM common_player_stats_by_game 
                JOIN player USING(player_ID) WHERE player_name = :p_name GROUP BY player_ID ORDER BY game_date";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_name', $var_playername);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
}

function onlyOnePlayer($playerid, $dbo)
{
    $var_playerID = $playerid;

    $query = "CALL search_career(:p_id)";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID);
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
        <p>Search career data by player name: </p>
        <div class="input"><label for="id_player">Player Name: </label>
            <input type="text" name="player_name" id="id_player"></div>
        <br/>
        <div class="input2"><input type="submit" name="field_submit" value="Submit"></div>
    </fieldset>
</form>
<?php
if (isset($_POST['field_submit'])) {
    if ($result && $prepared_stmt->rowCount() > 1) { ?>
        <br/><div class="question">
        <form method="post"></form>
        <h3 class="question"><b>Which player?</b></h3>
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
            </div>
        <?php } ?>
    <?php } else if ($result && $prepared_stmt->rowCount() == 1) {
        foreach ($result as $row) {
            onlyOnePlayer($row["player_ID"], $dbo);
        }
    } else { ?>
        <h3>Sorry, no results were found.</h3>
    <?php }
} ?>

<?php
if (isset($_POST['sub_submit'])) {
    $var_playerID = $_POST['player_id'];

    $query = "CALL search_career(:p_id)";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_id', $var_playerID);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
    output($result, $prepared_stmt);
}

function output($result, $prepared_stmt)
{
    if ($result && $prepared_stmt->rowCount() > 0) { ?>
        <div class="results"> Results:
            <?php foreach ($result as $row) { ?>
                <h6>Player Name: <?php echo $row["player_name_"]; ?></h6>
                <h6>First Game Date: <?php echo $row["first_game_date"]; ?></h6>
                <h6>Last Game Date: <?php echo $row["last_game_date"]; ?></h6>
                <h6>Total Plate Appearances: <?php echo $row["total_plate_app"]; ?></h6>
                <h6>Total At Bats: <?php echo $row["total_at_bat"]; ?></h6>
                <h6>Total Runs: <?php echo $row["total_runs"]; ?></h6>
                <h6>Total Hits: <?php echo $row["total_hits"]; ?></h6>
                <h6>Total Doubles: <?php echo $row["total_doubles"]; ?></h6>
                <h6>Total Triples: <?php echo $row["total_triples"]; ?></h6>
                <h6>Total Home Runs: <?php echo $row["total_homeruns"]; ?></h6>
                <h6>Total RBIS: <?php echo $row["total_RBIS"]; ?></h6>
                <h6>Total Strikeouts: <?php echo $row["total_strikeouts"]; ?></h6>
                <h6>Total Walks: <?php echo $row["total_walks"]; ?></h6>
                <h6>Batting Average: <?php echo $row["batting_average"]; ?></h6>
            <?php } ?>
        </div>
    <?php } else { ?>
        <h3>Sorry, an error occurred.</h3>
    <?php }
} ?>
</body>
</html>
