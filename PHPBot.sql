DROP TABLE user;
DROP TABLE message;

CREATE TABLE user (
  usr_id          INTEGER     PRIMARY KEY,

  usr_nick        VARCHAR(32) NOT NULL,
  usr_cur_nick    VARCHAR(32) NOT NULL,
  usr_chan        VARCHAR(32) NOT NULL,

  usr_join_qtt    INTEGER     NOT NULL DEFAULT 0, -- joins
  usr_quit_qtt    INTEGER     NOT NULL DEFAULT 0, -- quits
  usr_msg_qtt     INTEGER     NOT NULL DEFAULT 0, -- messages
  usr_act_qtt     INTEGER     NOT NULL DEFAULT 0, -- actions
  usr_botuse_qtt  INTEGER     NOT NULL DEFAULT 0, -- bot usage
  usr_kick_qtt    INTEGER     NOT NULL DEFAULT 0, -- kicks
  usr_nickchg_qtt INTEGER     NOT NULL DEFAULT 0, -- nick changes

  usr_join_dt     DATETIME    NOT NULL, -- last join
  usr_quit_dt     DATETIME    NULL, -- last quit
  usr_max_period  INTEGER     NULL, -- max time in channel (quit - join)

  usr_msg_dt      DATETIME    NULL, -- last message
  usr_act_dt      DATETIME    NULL, -- last action
  usr_botuse_dt   DATETIME    NULL, -- last bot usage
  usr_kick_dt     DATETIME    NULL, -- last kick
  usr_nickchg_dt  DATETIME    NULL, -- last nick change

  usr_upd_dt      DATETIME    NULL,
  usr_add_dt      DATETIME    NULL,

  UNIQUE(usr_nick, usr_chan)
);

CREATE TABLE message (
  usr_id          INTEGER     NOT NULL, -- FK user
  msg_id          INTEGER     PRIMARY KEY,
  msg_message     TEXT        NOT NULL,
  msg_add_dt      DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX index_User    ON user     (usr_nick, usr_chan);
CREATE        INDEX index_Message ON message  (msg_message);

CREATE TRIGGER trigger_UserInsert AFTER INSERT ON user
BEGIN
  UPDATE user SET
    usr_add_dt = DATETIME('NOW'),
    usr_upd_dt = DATETIME('NOW')
  WHERE
    rowid = new.rowid;
END;

CREATE TRIGGER trigger_UserUpdate AFTER UPDATE ON user
BEGIN
  UPDATE user SET
    usr_upd_dt = DATETIME('NOW')
  WHERE
    rowid = new.rowid;
END;

CREATE TRIGGER trigger_UserDelete AFTER DELETE ON user
BEGIN
  DELETE FROM message WHERE usr_id = old.rowid;
END;
