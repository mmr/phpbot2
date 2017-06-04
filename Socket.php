<?
final class Socket {
  private $socket = NULL;
  private $bytes_sent = 0;
  private $bytes_received = 0;
  const BUFSIZ = 512;

  public function __construct($servers){
    $this->Connect($servers);
  }

  public function __destruct(){
    $this->Disconnect();
  }

  private function Connect($servers){
    if($this->IsConnected()){ 
      throw new Exception('Already connected');
    }

    if(! is_array($servers) || count($servers) == 0){
      throw new Exception('No servers');
    }

    foreach($servers as $s){
      if(! ereg("[^:]+:[0-9]+", $s)){
        throw new Exception($s . ' is not a valid server');
      }
    }

    srand((float) microtime() * 1000000);
    shuffle($servers);

    foreach($servers as $s){
      list($server, $port) = explode(":", $s);
      $server = gethostbyname($server);
      $this->socket = fsockopen($server, $port, $errno, $err);
      if($this->IsConnected()){ 
        break;
      }
    }

    if($this->IsConnected()){ 
      return true;
    }
    else {
      throw new Exception($errno . ' - ' . $err);
    }
  }

  private function IsConnected(){
    return $this->socket;
  }

  public function Write($buf){
    $len = strlen($buf);
    $this->bytes_sent += $len;
    if($len>0){
      echo date('d/m/Y h:i:s') . " WRITE ($len): $buf";
    }
    return fputs($this->socket, $buf, $len);
  }

  public function Read(){
    $buf = fgets($this->socket, self::BUFSIZ);
    $len = strlen($buf);
    $this->bytes_received += $len;
    if($len>0){
      echo date('d/m/Y h:i:s') . " READ ($len): $buf";
    }
    return $buf;
  }

  public function GetBytesSent(){
    return $this->bytes_sent;
  }

  public function GetBytesReceived(){
    return $this->bytes_received;
  }

  public function Disconnect(){
    if($this->IsConnected()){
      fclose($this->socket);
      $this->socket = NULL;
      return true;
    }
    return false;
  }
}
?>
