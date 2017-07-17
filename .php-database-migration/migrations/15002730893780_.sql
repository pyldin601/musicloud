-- // 
-- Migration SQL that makes the change goes here.
INSERT INTO users VALUES (2, 'Guest',	'guest@homefs.biz', '', 0, 0, 0, 0, 0);

-- @UNDO
-- SQL to undo the change goes here.
DELETE FROM users WHERE id = 2;
