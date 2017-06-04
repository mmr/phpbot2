<?
abstract class BotOperation {
  private $trigger;

  final public function __construct($trigger){
    $this->trigger = $trigger;
  }

  final public function Test(BotEvent $event){
    $message = $event->GetMessage();
    if(preg_match("#^(\s*".$this->trigger."(\s+|$))#i", $message, $match)){
      $message = substr($message, strlen($match[1]));
      $event->SetMessage($message);
      return true;
    }
    return false;
  }

  final public function GetTrigger(){
    return $this->trigger;
  }

  abstract public function Reply(BotEvent $event);
}
?>
