<?
final class QuitOperation extends BotOperation {
  public function Reply(BotEvent $event){
    $sender   = $event->GetSender();

    if($event->GetIsPrivate()){
      $destination = $sender;
      $password = $event->GetMessage();

      if(! empty($password)){
        if($password === BotConfig::$PASSWORD["quit"]){
          $event->GetBot()->Disconnect();
          return;
        }
        else {
          $reply = "Senha incorreta.";
        }
      }
      else {
        $reply = "Uso: ".$this->GetTrigger()." senha";
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
