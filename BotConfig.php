<?
final class BotConfig {
  #public static $SERVERS  = array('localhost:6667');
  public static $SERVERS  = array('irc.brasnet.org:6667');
  public static $CHANNELS = array('#php');

  public static $INFO     = array(
    'user' => 'PHPBot2',
    'name' => 'http://phpbot2.src.b1n.org/',
    'nick' => 'PHPBot2',
    'pass' => 'papola22'
  );
  public static $AUTH_DELAY = 5;

  public static $PASSWORD = array(
    'op'    =>  'O',
    'voice' =>  'V',
    'quit'  =>  'Q',
    'join'  =>  'J',
    'part'  =>  'P'
  );

  public static $SQL_CONFIG_FILE = 'DBConfig.php';

  public static $OPERATIONS_DIR = 'operations';
  public static $OPERATIONS     = array(
    'Magic8Operation' => '!magic8',
    'StatsOperation'  => '!stats',
    'OpOperation'     => '!op',
    'VoiceOperation'  => '!voice',
    'QuitOperation'   => '!quit',
    'JoinOperation'   => '!join',
    'PartOperation'   => '!part',
    'SayOperation'    => '!say'
  );
}
?>
