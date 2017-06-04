<?
/*
 * $Id: PHPBot.php,v 1.18 2005/01/25 00:23:16 mmr Exp $
 * PHPBot <mmr@b1n.org>
 * Started in 2004/12/15
 */
require_once('BotConfig.php');
require_once('BotEvent.php');
require_once('BotMessage.php');
require_once('Socket.php');
require_once('IRC.php');
require_once('Logging.php');
require_once('SQLite.php');
require_once(BotConfig::$OPERATIONS_DIR.'/BotOperation.php');

final class PHPBot {
  private $servers;
  private $channels;
  private $info;

  private $operations;
  private $replies_counter;
  private $start_time;

  private $sql;
  private $logging;
  private $irc;

  private $users;

  public function __construct($servers, $channels, $info){
    try {
      $this->servers  = $servers;
      $this->channels = $channels;
      $this->info     = $info;

      $this->LoadOperations();
      $this->replies_counter = 1;
      $this->start_time = time();

      $this->sql  = new SQLite(BotConfig::$SQL_CONFIG_FILE);
      $this->log  = new Logging($this->sql);
      $this->Connect();
      $this->JoinChannels();

      while('I Love You'){
        $data = $this->GetData();
        $this->CheckForAction($data);
      }
    }
    catch(Exception $e) {
      echo "Exception Caught: " . $e->getMessage() . "\n";
      exit();
    }
  }

  private function Connect(){
    $this->irc = new IRC($this->servers, $this->info);
    sleep(BotConfig::$AUTH_DELAY);
  }

  private function Reconnect(){
    $this->Connect();
  }

  private function JoinChannels(){
    $channels = $this->channels;
    if(! is_array($channels) || count($channels) == 0){
      throw new Exception("No channels");
    }
    $this->irc->JoinChannel(implode(",", $channels));
  }

  private function LoadOperations(){
    foreach(BotConfig::$OPERATIONS as $operation => $trigger){
      $file = BotConfig::$OPERATIONS_DIR . "/" . $operation . ".php";

      if(! is_readable($file)){
        throw new Exception("Cannot load Operation $operation");
      }

      require_once($file);
      $this->operations[$operation] = new $operation($trigger);
    }
    return true;
  }

  private function GetData(){
    return $this->irc->GetData();
  }

  private function GetOperationsReply($destination, $sender, $message){
    $event = new BotEvent($this, $destination, $sender, $message);
    foreach($this->operations as $operation){
      if($operation->Test($event)){
        $reply = $operation->Reply($event);
        if(! is_null($reply)){
          $this->replies_counter++;
        }
        return $reply;
      }
    }
  }

  public function GetIRC(){
    return $this->irc;
  }

  public function GetSQL(){
    return $this->sql;
  }

  public function GetStartTime(){
    return $this->start_time;
  }

  public function GetRepliesCounter(){
    return $this->replies_counter;
  }

  public function Disconnect(){
    return $this->irc->Disconnect();
  }

  private function CheckForAction($data){
    if(empty($data)){
      return;
    }

    $regex = "#^(:\S+)?\s?(\S+)\s(\s?[^\r]+){0,15}\r\n$#";
    if(! preg_match($regex, $data, $match)){
      throw new Exception("Invalid Data: '$data'.");
    }

    list(, $prefix, $command, $params) = $match;

    $prefix = trim($prefix);
    if(empty($prefix)){
      switch($command){
      case 'PING':
        $server = substr($params, 1);
        $this->irc->PingReply($server);
        break;
      case 'ERROR':
        if(preg_match('/^:Closing Link:/', $params)){
          $this->Reconnect();
          $this->JoinChannels();
        }
      }
      return;
    }

    $re_special = "{\[`|^\]}_\\\-";
    $re_channel = "[\#+&][^\007 ,:]+";
    $re_nick = "[A-Za-z$re_special][\w$re_special]+";
    $re_user = "#^:($re_nick)![-~]?([^@]+)@(\S+)$#";

    switch($command){
    case 'PRIVMSG':
      if(preg_match($re_user, $prefix, $match)){
        list(, $sender, $login, $hostname) = $match;
      }
      else {
        throw new Exception("Invalid $command in '$params'.");
      }

      $regex = "#^($re_channel|$re_nick) :(.*)$#";
      if(preg_match($regex, $params, $match)){
        list(, $destination, $message) = $match;
        $reply = $this->GetOperationsReply($destination, $sender, $message);

        if(isset($this->users[$destination][$sender])){
          $this->log->LogMessage($this->users[$destination][$sender], $message);
        }
        if(! is_null($reply)){
          $this->irc->SendMessage($reply->GetDestination(), $reply->GetMessage());
        }
      }
      else {
        throw new Exception("Invalid $command in '$params'.");
      }
      break;
    case 'JOIN':
      if(preg_match($re_user, $prefix, $match)){
        list(, $sender, $login, $hostname) = $match;
      }
      else {
        throw new Exception("Invalid $command in '$prefix'.");
      }
      $channel = substr($params, 1);
      if($usr_id = $this->log->LogJoin($sender, $channel)){
        $this->users[$channel][$sender] = $usr_id;
      }
      break;
    case 'PART':
    case 'KICK':
    case 'QUIT':
      if(preg_match($re_user, $prefix, $match)){
        list(, $sender, $login, $hostname) = $match;
      }
      else {
        throw new Exception("Invalid $command in '$prefix'.");
      }
      $channel = substr($params, 1);
      if($usr_id = $this->log->LogQuit($sender, $channel)){
        if(isset($this->users[$channel][$sender])){
          unset($this->users[$channel][$sender]);
        }
      }
      break;
    case '353':
      $regex = "#^[^\#+&]+($re_channel) :((?:[@+]?$re_nick\s?)+)$#";
      if(preg_match($regex, $params, $match)){
        list(, $channel, $users) = $match;
        $users = explode(" ", preg_replace("/^\s+|@|\+|\s+$/", "", $users));
        if(is_array($users)){
          foreach($users as $user){
            if($user === BotConfig::$INFO['nick']){
              continue;
            }
            $this->users[$channel][$user] = 0;
          }
        }
      }
      else {
        throw new Exception("Invalid $command in '$data'.");
      }
      break;
    case '366':
      $regex = "#^[^\#+&]+($re_channel) :End#";
      if(preg_match($regex, $params, $match)){
        list(, $channel) = $match;

        $users = $this->users[$channel];
        if(is_array($users)){
          foreach($users as $user => $id){
            if($usr_id = $this->log->LogJoin($user, $channel)){
              $this->users[$channel][$user] = $usr_id;
            }
            else {
              throw new Exception("Could not get ID for user $user:$channel");
            }
          }
        }
      }
      else {
        throw new Exception("Invalid $command in '$data'.");
      }
      break;
    }
  }
}

$bot = new PHPBot(BotConfig::$SERVERS, BotConfig::$CHANNELS, BotConfig::$INFO);
?>
