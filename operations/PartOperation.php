<?
final class PartOperation extends BotOperation {
  public function Reply(BotEvent $event){
    $sender   = $event->GetSender();

    if($event->GetIsPrivate()){
      $destination = $sender;
      $message = $event->GetMessage();
      list($channel,$password) = explode(" ", $message);

      if(! empty($password)){
        if($password === BotConfig::$PASSWORD["part"]){
          $event->GetBot()->GetIRC()->PartChannel($channel);
          return;
        }
        else {
          $reply = "Senha incorreta.";
        }
      }
      else {
        $reply = "Uso: ".$this->GetTrigger()." #canal senha";
      }
    }
    else {
      $destination = $event->GetDestination();
      $reply = "$sender: essa operação deve ser feita em mensagem privada.";
    }

    return new BotMessage($destination, $reply);
  }
}
?>
