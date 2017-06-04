<?
final class StatsOperation extends BotOperation {
  public function Reply(BotEvent $event){
    $sender  = $event->GetSender();
    $uptime = time() - $event->GetBot()->GetStartTime();
    $replies_counter = $event->GetBot()->GetRepliesCounter();

    $time_units = array(
      'dia'     => 86400,
      'hora'    => 3600,
      'minuto'  => 60,
      'segundo' => 1);

    $tmp = $uptime;
    $msg = "";
    foreach($time_units as $name => $seconds){
      $calc = floor($tmp/$seconds);
      $tmp -= $calc*$seconds;

      if($calc>0){
        $msg .= "$calc $name";
        if($calc>1){
          $msg .= "s";
        }
        $msg .= " e ";
      }
    }

    $reply = "Estou vivo há $msg".
             "respondi à $replies_counter mensagens até agora.";

    if($event->GetIsPrivate()){
      $destination = $sender;
    }
    else {
      $reply = "$sender: $reply";
      $destination = $event->GetDestination();
    }

    return new BotMessage($destination, $reply);
  }
}
?>
