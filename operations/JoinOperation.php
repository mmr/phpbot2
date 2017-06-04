<?
final class JoinOperation extends BotOperation {
  public function Reply(BotEvent $event){
    $sender = $event->GetSender();

    if($event->GetIsPrivate()){
      $destination = $sender;
      $message = $event->GetMessage();
      $message = explode(" ", $message);

      if(count($message) >= 3){
        list($channel, $password) = $message;
        if(! empty($password)){
          if($password === BotConfig::$PASSWORD["join"]){
            $event->GetBot()->GetIRC()->JoinChannel($channel);
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
