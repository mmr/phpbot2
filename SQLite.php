<?
final class SQLite
{
  private $link;

  public function __construct($config_file){
    if(is_readable($config_file)){
      $this->Connect($config_file);
    }
    else {
      throw new Exception("Could not open $config_file.");
    }
  }

  public function __destruct(){
    $this->Disconnect();
  }

  private function Connect($config_file){
    if($this->IsConnected()){ 
      throw new Exception("Already connected to DB.");
    }

    require_once($config_file);
    if($this->link = sqlite_popen(DBConfig::$FILE, DBConfig::$MODE, $error)){
      return true;
    }
    else {
      throw new Exception("Could not connect to DB : $error.");
    }
  }

  private function IsConnected(){
    return $this->link;
  }

  public function SingleQuery($query){
    if(! $this->IsConnected()){
      throw new Exception("Not Connected to DB.");
    }

    #echo "$query\n";
    $ret = sqlite_array_query($query, $this->link, SQLITE_ASSOC);
    if(count($ret)){
      return $ret[0];
    }
  }

  public function Query($query){
    if(! $this->IsConnected()){
      throw new Exception("Not Connected to DB.");
    }
    #echo "$query\n";

    #$result = sqlite_unbuffered_query($query, $this->link);
    $result = sqlite_query($query, $this->link);

    if(! $result){
      throw new Exception("Query Failed: '$query'");
    }

    $num = sqlite_num_rows($result);

    if($num > 0){
      for($i=0; $i<$num; $i++){
        $rows[$i] = sqlite_fetch_array($result, SQLITE_ASSOC);
      }

      return $rows;
    }
    else {
      return sqlite_last_insert_rowid($this->link);
    }
  }

  private function Disconnect(){
    if(! is_null($this->link)){
      return sqlite_close($this->link);
    }

    return false;
  }

  public function FormatForDB($var, $delim="'"){
    $var = trim($var);

    if(strlen($var)==0 || is_null($var)){
      return 'NULL';
    }

    return $delim . addslashes($var) . $delim;
  }
}
?>
