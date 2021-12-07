-- Josh Ludwig, Lloyd Abernathy
-- joshua.r.ludwig@vanderbilt.edu, 
-- lloyd.r.abernathy@vanderbilt.edu
-- Project 2

DROP DATABASE IF EXISTS baseballdb;
CREATE DATABASE IF NOT EXISTS baseballdb;
USE baseballdb;

DROP TABLE IF EXISTS megatable;
CREATE TABLE IF NOT EXISTS megatable (
	player_ID VARCHAR(20),
    player_name TINYTEXT, # need to fix player names that contain special characters
    game_date VARCHAR(20), # some dates are not in date format, therefore VARCHAR instead of DATE
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
    fanduel_fantasy DECIMAL(5,2)
) ENGINE = INNODB;

LOAD DATA LOCAL INFILE 'C:/Users/ludwj/Downloads/mlbbatting1901-2021.csv'  INTO TABLE megatable 
CHARACTER SET latin1 FIELDS TERMINATED BY ',' ENCLOSED BY '"'
LINES TERMINATED BY '\r\n'
IGNORE 1 LINES;

-- LOAD DATA LOCAL INFILE 'C:/Users/lloydabernathy/Downloads/mlbbatting1901-2021.csv'  INTO TABLE megatable 
-- CHARACTER SET latin1 FIELDS TERMINATED BY ',' ENCLOSED BY '"'
-- LINES TERMINATED BY '\r\n'
-- IGNORE 1 LINES;

-- SELECT *
-- FROM megatable
-- ORDER BY game_date DESC;

-- Removing duplicate rows from megatable
DROP TABLE IF EXISTS nodupes;
CREATE TABLE IF NOT EXISTS nodupes LIKE megatable;
INSERT INTO nodupes
	SELECT DISTINCT * FROM megatable;
DROP TABLE megatable;
RENAME TABLE nodupes TO megatable;

-- Removing data inaccuracies
SET SQL_SAFE_UPDATES = 0;
DELETE FROM megatable
WHERE player_name = 'Javier Baez' AND game_date = '2021-04-11' AND player_team = 'NYM';
DELETE FROM megatable
WHERE player_name = 'Bobby Bonilla' AND game_date = '1986-04-20' AND player_team = 'PIT';
DELETE FROM megatable
WHERE player_name = 'Tom Foley' AND game_date = '1986-07-13' AND player_team = 'MON';
DELETE FROM megatable
WHERE player_name = 'Ken Griffey Jr.' AND game_date = '2008-04-28' AND player_team = 'CHW';
DELETE FROM megatable
WHERE player_name = 'Cliff Johnson' AND game_date = '1980-05-28' AND player_team = 'CHC';
DELETE FROM megatable
WHERE player_name = 'Luis Polonia' AND game_date = '1995-05-04' AND player_team = 'ATL';
DELETE FROM megatable
WHERE player_name = 'Bill Virdon' AND game_date = '1956-05-13 (2)' AND player_team = 'PIT';
DELETE FROM megatable
WHERE player_name = 'Joel Youngblood' AND game_date = '1982-08-04' AND player_team = 'MON';

-- Query should return 0 rows
SELECT *
FROM megatable
GROUP BY player_ID, player_name, game_date
HAVING count(*) > 1;


-- Stored Procedure to get Career Stats based on player_id
DELIMITER //
DROP PROCEDURE IF EXISTS search_career //
CREATE PROCEDURE search_career(IN ID VARCHAR(20))
BEGIN
	DECLARE player_name_ TINYTEXT;
    DECLARE first_game_date VARCHAR(20);
    DECLARE last_game_date VARCHAR(20);
    DECLARE total_plate_app BIGINT UNSIGNED;
    DECLARE total_at_bat BIGINT UNSIGNED;
    DECLARE total_runs BIGINT UNSIGNED;
    DECLARE total_hits BIGINT UNSIGNED;
    DECLARE total_doubles BIGINT UNSIGNED;
    DECLARE total_triples BIGINT UNSIGNED;
    DECLARE total_homeruns BIGINT UNSIGNED;
    DECLARE total_RBIS BIGINT UNSIGNED;
    DECLARE total_strikeouts BIGINT UNSIGNED;
    DECLARE total_walks BIGINT UNSIGNED;
    DECLARE batting_average DECIMAL(4,3);
    
    SELECT player_name INTO player_name_ FROM player WHERE player_ID = ID LIMIT 1;
    SELECT game_date INTO first_game_date FROM common_player_stats_by_game WHERE player_ID = ID ORDER BY game_date LIMIT 1;
    SELECT game_date INTO last_game_date FROM common_player_stats_by_game WHERE player_ID = ID ORDER BY game_date DESC LIMIT 1;
    
    SELECT SUM(plate_app), SUM(at_bat), SUM(runs), SUM(hits), SUM(doubles), SUM(triples), SUM(homeruns), SUM(RBIS), SUM(strikeouts), SUM(base_on_balls)
    INTO total_plate_app, total_at_bat, total_runs, total_hits, total_doubles, total_triples, total_homeruns, total_RBIS, total_strikeouts, total_walks
    FROM common_player_stats_by_game WHERE player_ID = ID;
    
    SET batting_average = total_hits / total_at_bat;
    
    SELECT player_name_, first_game_date, last_game_date, total_plate_app, total_at_bat, 
    total_runs, total_hits, total_doubles, total_triples, total_homeruns, total_RBIS, total_strikeouts, total_walks, batting_average;
END //
DELIMITER ;
-- CALL search_career('altuvjo01');


-- Views for Leaderboard Display
CREATE OR REPLACE VIEW atbatsLB AS
SELECT player_name, SUM(plate_app) AS total_at_bat
FROM common_player_stats_by_game 
	JOIN player USING (player_ID)
GROUP BY player_ID
ORDER BY total_at_bat DESC;

CREATE OR REPLACE VIEW hitsLB AS
SELECT player_name, SUM(hits) AS total_hit
FROM common_player_stats_by_game
	JOIN player USING (player_ID)
GROUP BY player_ID
ORDER BY total_hit DESC;

CREATE OR REPLACE VIEW homerunsLB AS
SELECT player_name, SUM(homeruns) AS total_homeruns
FROM common_player_stats_by_game
	JOIN player USING (player_ID)
GROUP BY player_ID
ORDER BY total_homeruns DESC;

CREATE OR REPLACE VIEW RBIsLB AS
SELECT player_name, SUM(RBIs) AS total_RBIs
FROM common_player_stats_by_game
	JOIN player USING (player_ID)
GROUP BY player_ID
ORDER BY total_RBIs DESC;

-- SELECT * FROM atbatsLB;
-- SELECT * FROM hitsLB;
-- SELECT * FROM homerunsLB;
-- SELECT * FROM RBIsLB;


-- Triggers for player deletions
DROP TRIGGER IF EXISTS remove_from_common_table;
DELIMITER //
CREATE TRIGGER remove_from_common_table
AFTER DELETE
ON player
FOR EACH ROW
BEGIN
	DELETE FROM common_player_stats_by_game
    WHERE player_ID = OLD.player_ID;
END //
DELIMITER ;

DROP TRIGGER IF EXISTS remove_from_uncommon_table;
DELIMITER //
CREATE TRIGGER remove_from_uncommon_table
AFTER DELETE
ON player
FOR EACH ROW
BEGIN
	DELETE FROM uncommon_player_stats_by_game
    WHERE player_ID = OLD.player_ID;
END //
DELIMITER ;

-- DELETE FROM player WHERE player_ID = "rosepe01";
-- select * from common_player_stats_by_game where player_ID = "rosepe01";


-- Stored Procedure to create new game entry based on player_id, game_date, and other attributes
DELIMITER //
DROP PROCEDURE IF EXISTS create_entry //
CREATE PROCEDURE create_entry(IN ID VARCHAR(20), IN gamedate VARCHAR(20), IN playerteam CHAR(3), IN opposingteam CHAR(3), 
								IN teamscore TINYINT UNSIGNED, IN opposingscore TINYINT UNSIGNED, IN result CHAR(1))
BEGIN
	DECLARE gameresult VARCHAR(10);
    SET gameresult = CONCAT(result, ' ', teamscore, '-', opposingscore);
    
    INSERT INTO common_player_stats_by_game(player_ID, game_date, player_team, opp_team, game_result, plate_app, at_bat, 
    runs, hits, doubles, triples, homeruns, RBIs, base_on_balls, int_base_on_balls, strikeouts, hit_by_pitch)
    VALUES(ID, gamedate, playerteam, opposingteam, gameresult, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    
END //
DELIMITER ;
-- CALL create_entry('abbotco01', '2022-01-01', 'HOU', 'ATL', 4, 5, 'L');
-- select * from common_player_stats_by_game where game_date = '2022-01-01';
-- delete from common_player_stats_by_game where player_ID = 'abbotco01' and game_date = '2022-01-01';

-- select count(*) from megatable;
-- select count(*) from common_player_stats_by_game;
-- select count(*) from uncommon_player_stats_by_game;
-- select count(*) from player;
-- select * from megatable order by game_date desc;