<?
final class BotMessage {
  private $destination;
  private $message;
  private $is_action;

  public function __construct($destination, $message, $is_action = false){
    $this->destination  = $destination;
    $this->message      = $message;
    $this->is_action    = $is_action;
  }

  public function GetDestination(){
    return $this->destination;
  }

  public function GetMessage(){
    return $this->message;
  }

  public function IsAction(){
    return $this->is_action;
  }
}
?>
