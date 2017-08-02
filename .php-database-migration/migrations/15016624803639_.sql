-- // 
-- Migration SQL that makes the change goes here.
ALTER TABLE video_dl_queue
  DROP CONSTRAINT video_dl_queue_track_id_fkey,
  ADD CONSTRAINT video_dl_queue_track_id_fkey
    FOREIGN KEY (track_id)
    REFERENCES songs (id)
    ON DELETE CASCADE;

-- @UNDO
-- SQL to undo the change goes here.
ALTER TABLE video_dl_queue
  DROP CONSTRAINT video_dl_queue_track_id_fkey,
  ADD CONSTRAINT video_dl_queue_track_id_fkey
    FOREIGN KEY (track_id)
    REFERENCES songs (id);
