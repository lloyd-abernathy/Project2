DELIMITER //
CREATE PROCEDURE search_player(IN player_input TEXT)
Begin 
	DECLARE sql_error int DEFAULT FALSE;
    
    DECLARE continue handler for SQLEXCEPTION SET sql_error = true;
    
    IF player_input = ""
    THEN set sql_error = true;
    END IF;

    select player_ID
    where player_name = player_input;

END//