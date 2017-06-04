<?
final class BotEvent {
  private $bot;
  private $destination;
  private $sender;
  private $message;
  private $is_private;

  public function __construct($bot, $destination, $sender, $message){
    $this->bot = $bot;
    $this->destination = $destination;
    $this->sender   = $sender;
    $this->message  = $message;
    $this->is_private = (strtolower($destination) === strtolower(BotConfig::$INFO['nick']));
  }

  public function GetBot(){
    return $this->bot;
  }

  public function GetDestination(){
    return $this->destination;
  }

  public function GetSender(){
    return $this->sender;
  }

  public function GetMessage(){
    return $this->message;
  }

  public function GetIsPrivate(){
    return $this->is_private;
  }

  public function SetMessage($message){
    return $this->message = $message;
  }
}
?>
