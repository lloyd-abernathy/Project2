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

SELECT *
FROM megatable
ORDER BY game_date DESC;

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



select count(*) from megatable;

