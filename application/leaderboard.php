<?php
require_once("conn.php");
if (isset($_POST['field_submit'])) {
    $var_type = $_POST['lb-type'];

    if ($var_type == 'total_at_bat') {
        $query = "SELECT * FROM atbatsLB LIMIT 500";
    } else if ($var_type == 'total_hit') {
        $query = "SELECT * FROM hitsLB LIMIT 500";
    } else if ($var_type == 'total_homeruns') {
        $query = "SELECT * FROM homerunsLB LIMIT 500";
    } else if ($var_type == 'total_RBIs') {
        $query = "SELECT * FROM RBIsLB LIMIT 500";
    }

    try {
        $prepared_stmt = $dbo->prepare($query);
        $prepared_stmt->execute();
        $result = $prepared_stmt->fetchAll();
    } catch (PDOException $ex) {
        echo $sql . "<br>" . $error->getMessage();
    }
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
        <p>Choose an all-time leaderboard statistic: </p>
        <div class="input"><label for="leaderboard">All-Time Leaderboard: </label>
            <select name="lb-type" id="leaderboard">
                <option value="total_at_bat">At Bats</option>
                <option value="total_hit">Hits</option>
                <option value="total_homeruns">Home Runs</option>
                <option value="total_RBIs">RBIs</option>
            </select></div>
        <br/>
        <div class="input2"><input type="submit" name="field_submit" value="Submit"></div>
    </fieldset>
</form>
<?php
if (isset($_POST['field_submit'])) {
    if ($result && $prepared_stmt->rowCount() > 0) { ?>
        <div class="results"> Results:
            <table>
                <thead>
                <tr>
                    <th>Player Name</th>
                    <th><?php echo $var_type; ?></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php echo $row["player_name"]; ?></td>
                        <td><?php echo $row["$var_type"]; ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <h3>Sorry, no results were found.</h3>
    <?php }
} ?>

</body>
</html>
