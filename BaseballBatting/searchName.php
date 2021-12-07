<?php
if (isset($_POST['field_submit'])) {
    require_once("conn.php");
    $var_playername = $_POST['player_name'];

    $query = "SELECT game_date, player_team, opp_team,
    game_result FROM megatable WHERE player_name = :p_name";

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->bindValue('p_name', $var_playername);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    }
    catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
}
?>


<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Baseball Batting</title>
    <link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
<div id="navbar">
    <ul>
        <li><a href="searchName.php">Search by Player</a></li>
        <li><a href="searchDate.php">Search by Date</a></li>
    </ul>
</div>

<h1> Baseball Batting Database </h1>

<form method="post">
    <p>Search game data by player name: </p>
    <label for="id_player">Player Name</label>
    <input type="text" name="player_name" id="id_player">
    <input type="submit" name="field_submit" value="Submit">
</form>
<?php
if (isset($_POST['field_submit'])) {
    if ($result && $prepared_stmt->rowCount() > 0) { ?>
        <h2>Results</h2>
        <table>
            <thead>
            <tr>
                <th>Game Date</th>
                <th>Player Team</th>
                <th>Opposing Team</th>
                <th>Final Result</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($result as $row) { ?>
                <tr>
                    <td><?php echo $row["game_date"]; ?></td>
                    <td><?php echo $row["player_team"]; ?></td>
                    <td><?php echo $row["opp_team"]; ?></td>
                    <td><?php echo $row["game_result"]?></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <h3>Sorry, no results were found.</h3>
    <?php }
} ?>

</body>
</html>