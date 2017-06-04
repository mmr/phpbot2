<?
final class SayOperation extends BotOperation {
  public function Reply(BotEvent $event){
    if($event->GetIsPrivate()){
      return;
    }

    $message  = $event->GetMessage();
    $reply = trim($message);

    if(! empty($reply)){
      return new BotMessage($event->GetDestination(), $reply);
    }
  }
}
?>
