<?
class IRC {
  private $sock = NULL;

  public function __construct($servers, $info){
    $this->Connect($servers, $info);
  }

  public function __destruct(){
    $this->Disconnect();
  }

  private function Connect($servers, $info){
    return ($this->sock = new Socket($servers)) && $this->Identify($info);
  }

  private function Identify($info){
    if(! is_array($info) || count($info) == 0){
      throw new Exception('No info');
    }

    if(! isset($info['user']) || empty($info['user']) ||
       ! isset($info['name']) || empty($info['name']) ||
       ! isset($info['nick']) || empty($info['nick']) ||
       ! isset($info['pass']) || empty($info['pass']))
    {
      throw new Exception('Invalid info');
    }

    return
      $this->sock->Write("USER " . $info['user'] . " 8 * :" . $info['name'] . "\r\n") &&
      $this->sock->Write("NICK " . $info['nick'] . "\r\n") &&
      $this->sock->Write("NICKSERV IDENTIFY " . $info['pass'] . "\r\n");
  }

  public function JoinChannel($channel){
    return $this->sock->Write("JOIN $channel\r\n");
  }

  public function PartChannel($channel){
    return $this->sock->Write("Part $channel\r\n");
  }

  public function GetData(){
    return $this->sock->Read();
  }

  public function PingReply($server){
    return $this->sock->Write("PONG $server\r\n");
  }

  public function SendMessage($destination, $message = array()){
    if(! is_array($message)){
      if(empty($message)){
        return false;
      }
      $message = array($message);
    }
    foreach($message as $m){
      $this->sock->Write("PRIVMSG $destination :$m\r\n");
    }
    return true;
  }

  public function Mode($channel, $nick, $mode){
    $this->sock->Write("MODE $channel $mode $nick\r\n");
  }

  public function Quit($message = ''){
    $this->sock->Write("QUIT $message\r\n");
  }

  public function Disconnect(){
    $this->Quit();
    $this->sock->Disconnect();
  }
}
?>
