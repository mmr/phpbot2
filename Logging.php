<?
final class Logging {
  private $sql;

  public function __construct($sql){
    $this->sql = $sql;
  }

  public function LogJoin($nick, $chan){
    try {
      $this->sql->Query("BEGIN TRANSACTION");

      $query = "
        SELECT usr_id FROM user
        WHERE
          usr_nick = " . $this->sql->FormatForDb($nick) . " AND
          usr_chan = " . $this->sql->FormatForDb($chan);

      $ret = $this->sql->SingleQuery($query);

      if(count($ret)){
        $usr_id = $ret['usr_id'];

        $query = "
          UPDATE user SET
            usr_join_dt  = DATETIME('NOW'),
            usr_join_qtt = usr_join_qtt + 1
          WHERE
            usr_id = $usr_id";

        $this->sql->Query($query);
      }
      else {
        $query = "
          INSERT INTO user (
            usr_nick,
            usr_cur_nick,
            usr_chan,
            usr_join_qtt,
            usr_join_dt)
          VALUES (
            " . $this->sql->FormatForDb($nick) . ",
            " . $this->sql->FormatForDb($nick) . ",
            " . $this->sql->FormatForDb($chan) . ",
            1,
            DATETIME('NOW')
          )";

        $usr_id = $this->sql->Query($query);
      }
      $this->sql->Query("COMMIT TRANSACTION");
      return $usr_id;
    }
    catch(Exception $e){
      echo $e->getMessage()."\n";
      $this->sql->Query("ROLLBACK TRANSACTION");
    }
    return false;
  }

  public function LogQuit($nick, $chan){
    try {
      $this->sql->Query("BEGIN TRANSACTION");

      $query = "
        SELECT usr_id FROM user
        WHERE
          usr_nick = " . $this->sql->FormatForDb($nick) . " AND
          usr_chan = " . $this->sql->FormatForDb($chan);

      $ret = $this->sql->SingleQuery($query);

      if(count($ret)){
        $usr_id = $ret['usr_id'];

        $query = "
          UPDATE user SET
            usr_quit_dt  = DATETIME('NOW'),
            usr_quit_qtt = usr_quit_qtt + 1
          WHERE
            usr_id = $usr_id";

        $this->sql->Query($query);
      }
      else {
        $query = "
          INSERT INTO user (
            usr_nick,
            usr_cur_nick,
            usr_chan,
            usr_join_qtt,
            usr_join_dt,
            usr_quit_qtt,
            usr_quit_dt)
          VALUES (
            " . $this->sql->FormatForDb($nick) . ",
            " . $this->sql->FormatForDb($nick) . ",
            " . $this->sql->FormatForDb($chan) . ",
            1, DATETIME('NOW'),
            1, DATETIME('NOW')
          )";

        $usr_id = $this->sql->Query($query);
      }
      $this->sql->Query("COMMIT TRANSACTION");
      return $usr_id;
    }
    catch(Exception $e){
      echo $e->getMessage()."\n";
      $this->sql->Query("ROLLBACK TRANSACTION");
    }
    return false;
  }

  public function LogMessage($usr_id, $message){
    try {
      $this->sql->Query("BEGIN TRANSACTION");
      $query = "
        UPDATE user SET
          usr_msg_qtt = usr_msg_qtt + 1,
          usr_msg_dt  = DATETIME('NOW')
        WHERE
          usr_id = $usr_id";
      $this->sql->Query($query);

      $query = "
        INSERT INTO message (
          usr_id,
          msg_message,
          msg_add_dt)
        VALUES (
          $usr_id,
          '$message',
          DATETIME('NOW')
        )";
      $this->sql->Query($query);
      $this->sql->Query("COMMIT TRANSACTION");
      return true;
    }
    catch(Exception $e){
      echo $e->getMessage()."\n";
      $this->sql->Query("ROLLBACK TRANSACTION");
    }
    return false;
  }

  public function LogBotUsage($usr_id, $message){
    try {
      $this->sql->Query("BEGIN TRANSACTION");

      $query = "
        UPDATE user SET
          usr_botuse_qtt = usr_botuse_qtt + 1,
          usr_botuse_dt  = DATETIME('NOW')
        WHERE
          usr_id = $usr_id";

      $this->sql->Query($query);
      $this->sql->Query("COMMIT TRANSACTION");
      return true;
    }
    catch(Exception $e){
      echo $e->getMessage()."\n";
      $this->sql->Query("ROLLBACK TRANSACTION");
    }
    return false;
  }
}
