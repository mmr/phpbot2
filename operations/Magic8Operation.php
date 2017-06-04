<?
final class Magic8Operation extends BotOperation {
  private $responses =
    array('Sim.',
          'Hmm, sim...',
          'Definitivamente!',
          'Com certeza!',
          'Yep!',
          'Acredito que sim.',
          'Com certeza!',
          'Claro!',

          'Talvez.',
          'Provavelmente sim...',
          'Provavelmente n�o...',
          '� poss�vel...',
          '�s vezes...',
          'Somente �s ter�as-feiras.',
          'Se eu te disser vou ter de te matar!',
          'N�o fa�a nada que eu n�o faria.',

          'N�o.',
          'Hmm, n�o...',
          'Definitivamente n�o!',
          'Sem chance.',
          'Nope!',
          'Acredito que n�o.',
          '...',
          'De jeito nenhum!');

  public function Reply(BotEvent $event){
    $sender   = $event->GetSender();
    $message  = $event->GetMessage();

    if(preg_match("/([^\s]+)\s+ou\s+([^\s?]+)/i", $message)){
      $responses = spliti(" ou ", $message);
    }
    else {
      $responses = $this->responses;
    }
    $response = mt_rand(0, count($responses)-1);
    $reply    = $responses[$response];
    $reply    = trim(str_replace("?", "", $reply));

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
