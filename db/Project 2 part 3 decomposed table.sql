DROP TABLE IF EXISTS player;
CREATE TABLE IF NOT EXISTS player(
	player_ID VARCHAR(20) PRIMARY KEY,
    player_name TINYTEXT
);

INSERT INTO player(
	player_ID,
    player_name
)
SELECT DISTINCT 
	player_ID,
    player_name
FROM 
	megatable;


DROP TABLE IF EXISTS common_player_stats_by_game;
CREATE TABLE IF NOT EXISTS common_player_stats_by_game(
	player_ID VARCHAR(20),
    game_date VARCHAR(20),
    player_team CHAR(3),
	opp_team CHAR(3),
    game_result VARCHAR(10),
	plate_app TINYINT UNSIGNED,
    at_bat TINYINT UNSIGNED,
    runs TINYINT UNSIGNED,
    hits TINYINT UNSIGNED,
    doubles TINYINT UNSIGNED,
    triples TINYINT UNSIGNED,
    homeruns TINYINT UNSIGNED,
    RBIs TINYINT UNSIGNED,
    base_on_balls TINYINT UNSIGNED,
    int_base_on_balls TINYINT UNSIGNED,
    strikeouts TINYINT UNSIGNED,
    hit_by_pitch TINYINT UNSIGNED,
    PRIMARY KEY (player_ID, game_date)
);

INSERT INTO common_player_stats_by_game(
	player_ID,
    game_date,
    player_team,
    opp_team,
    game_result,
    plate_app,
    at_bat,
    runs,
    hits,
    doubles,
    triples,
    homeruns,
    RBIs,
    base_on_balls,
    int_base_on_balls,
    strikeouts,
    hit_by_pitch
)
SELECT 
	player_ID,
    game_date,
    player_team,
    opp_team,
    game_result,
    plate_app,
    at_bat,
    runs,
    hits,
    doubles,
    triples,
    homeruns,
    RBIs,
    base_on_balls,
    int_base_on_balls,
    strikeouts,
    hit_by_pitch
FROM megatable;


DROP TABLE IF EXISTS uncommon_player_stats_by_game;
CREATE TABLE IF NOT EXISTS uncommon_player_stats_by_game(
	player_ID VARCHAR(20),
    game_date VARCHAR(20),
	sacrifice_hits TINYINT UNSIGNED,
    sacrifice_flies TINYINT UNSIGNED,
    reached_on_error TINYINT UNSIGNED,
    grounded_into_double_play TINYINT UNSIGNED,
    stolen_bases TINYINT UNSIGNED,
    caught_stealing TINYINT UNSIGNED,
    win_prob_added DECIMAL(4,3),
    base_out_runs_added DECIMAL(4,3),
    average_leverage_index DECIMAL(4,3),
    batting_order_position TINYINT UNSIGNED,
    position VARCHAR(3),
    draftkings_fantasy DECIMAL(5,2),
    fanduel_fantasy DECIMAL(5,2),
    PRIMARY KEY (player_ID, game_date)
);

INSERT INTO uncommon_player_stats_by_game(
	player_ID,
    game_date,
    sacrifice_hits,
    sacrifice_flies,
    reached_on_error,
    grounded_into_double_play,
    stolen_bases,
    caught_stealing,
    win_prob_added,
    base_out_runs_added,
    average_leverage_index,
    batting_order_position,
    position,
    draftkings_fantasy,
    fanduel_fantasy
)
SELECT 
	player_ID,
    game_date,
    sacrifice_hits,
    sacrifice_flies,
    reached_on_error,
    grounded_into_double_play,
    stolen_bases,
    caught_stealing,
    win_prob_added,
    base_out_runs_added,
    average_leverage_index,
    batting_order_position,
    position,
    draftkings_fantasy,
    fanduel_fantasy
FROM
	megatable;


DROP TABLE IF EXISTS player_career_stats;
CREATE TABLE IF NOT EXISTS player_career_stats(
	player_ID VARCHAR(20) PRIMARY KEY,
    plate_app TINYINT UNSIGNED,
    at_bat TINYINT UNSIGNED,
    runs TINYINT UNSIGNED,
    hits TINYINT UNSIGNED,
    doubles TINYINT UNSIGNED,
    triples TINYINT UNSIGNED,
    homeruns TINYINT UNSIGNED,
    RBIs TINYINT UNSIGNED,
    base_on_balls TINYINT UNSIGNED,
    int_base_on_balls TINYINT UNSIGNED,
    strikeouts TINYINT UNSIGNED,
    hit_by_pitch TINYINT UNSIGNED,
    sacrifice_hits TINYINT UNSIGNED,
    sacrifice_flies TINYINT UNSIGNED,
    reached_on_error TINYINT UNSIGNED,
    grounded_into_double_play TINYINT UNSIGNED,
    stolen_bases TINYINT UNSIGNED,
    caught_stealing TINYINT UNSIGNED
);

INSERT INTO player_career_stats(
	player_ID,
    plate_app,
    at_bat,
    runs,
    hits,
    doubles,
    triples,
    homeruns,
    RBIs,
    base_on_balls,
    int_base_on_balls,
    strikeouts,
    hit_by_pitch,
    sacrifice_hits,
    sacrifice_flies,
    reached_on_error,
    grounded_into_double_play,
	stolen_bases,
    caught_stealing)
SELECT 
	player_ID,
    plate_app,
    at_bat,
    runs,
    hits,
    doubles,
    triples,
    homeruns,
    RBIs,
    base_on_balls,
    int_base_on_balls,
    strikeouts,
    hit_by_pitch,
    sacrifice_hits,
    sacrifice_flies,
    reached_on_error,
    grounded_into_double_play,
	stolen_bases,
    caught_stealing
FROM 
	megatable
GROUP BY
	player_ID;


SELECT * FROM common_player_stats_by_game;
SELECT * FROM uncommon_player_stats_by_game;
-- SELECT * FROM player_career_stats;
SELECT * FROM player;
SELECT * FROM megatable;