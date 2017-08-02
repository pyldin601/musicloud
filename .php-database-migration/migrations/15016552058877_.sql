-- // 
-- Migration SQL that makes the change goes here.
CREATE TABLE video_dl_queue (
  id SERIAL PRIMARY KEY,
  url TEXT NOT NULL,
  user_id INTEGER NOT NULL REFERENCES users (id),
  track_id CHARACTER(10) NOT NULL REFERENCES songs (id),
  status INTEGER DEFAULT 0 NOT NULL
);

-- @UNDO
-- SQL to undo the change goes here.
DROP TABLE video_dl_queue;
