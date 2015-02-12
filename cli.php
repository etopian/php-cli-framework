<?php
/**
 * Command line interface
 * License: MIT
 * Allows easily writing OOP CLI scripts in PHP
 * @author sami
 *
 */
abstract class CLI{
  
  private $appname = 'CLI Framework';
  private $author = 'Sami Khan';
  private $copyright = '(c) 2011 Sami Khan';
  
  
  function __construct($appname = null, $author = null, $copyright = null) {
    if($appname){
      $this->appname = $appname;
    }
    if($author){
      $this->author = $author;
    }
    if($copyright){
      $this->copyright = $copyright;
    }
    
    if($this::isCli()){
      $args = $this::parseArgs();
      foreach($args['flags'] as $flag){
        $method_name = 'flag_'.$flag;
        if(method_exists($this, $method_name)){
          call_user_func(array($this, $method_name));
          //
        }
      }
      foreach($args['arguments'] as $arg){
        $method_name = 'argument_'.$arg;
        if(method_exists($this, $method_name)){
          call_user_func(array($this, $method_name));
        }
      }
      
      foreach($args['options'] as $arg){
        
        if(is_string($arg) === false && isset($arg[0]) && isset($arg[1])){
          
          $method_name = 'option_'.$arg[0];
          if(method_exists($this, $method_name)){
            call_user_func(array($this, $method_name), $arg[1]);
          }
        }else{
          $method_name = 'option_'.$arg;
          if(method_exists($this, $method_name)){
            call_user_func(array($this, $method_name));
          }
        }
      }
      global $argv;
      if(count($argv) === 1){
        $this->help();
      }else{
        $this->main();
      }
      exit();
    }
  }  
  
  /**
   * Set the name of the app
   */
  public function setAppName($appname){
     $this->appname = $appname;
  }
  

  /**
   * Simply test whether or not we are running in CLI mode.
   */
  public static function isCli(){
    if(!defined('STDIN') && self::isCgi()) {

      if(getenv('TERM')) {
        return true;
      }
      return false;
    }
    return defined('STDIN');
  }
  

  public function getInput($question = "Are you sure you want to do this?  Type 'yes' to continue:"){
    echo $question." ";
    $handle = fopen ("php://stdin","r");
    $line = fgets($handle);
    $answer = trim($line);
    return $answer;   
  }
  
  
  /**
  
  Example input: ./script.php -a arg1 --opt1 arg2 -bcde --opt2=val2 arg3 arg4 arg5 -fg --opt3
  Example output:
  Array
  (
    [exec] => ./script.php
    [options] => Array
    (
      [0] => opt1
      [1] => Array
      (
        [0] => opt2
        [1] => val2
      )
      [2] => opt3
    )
    [flags] => Array
    (
      [0] => a
      [1] => b
      [2] => c
      [3] => d
      [4] => e
      [5] => f
      [6] => g
    )
    [arguments] => Array
    (
      [0] => arg1
      [1] => arg2
      [2] => arg3
      [3] => arg4
      [4] => arg5
    )
  )
  */  
  public static function parseArgs($args = null) {
    if($args == null){ 
      global $argv,$argc;
      $args = $argv; 
    }
    $ret = array(
          'exec'      => '',
          'options'   => array(),
          'flags'     => array(),
          'arguments' => array(),
    );
    if(count($args) == 0){
      return $ret;
    }
    
    $ret['exec'] = array_shift( $args );
  
    while (($arg = array_shift($args)) != NULL) {
      // Is it a option? (prefixed with --)
      if ( substr($arg, 0, 2) === '--' ) {
        $option = substr($arg, 2);
  
        // is it the syntax '--option=argument'?
        if (strpos($option,'=') !== FALSE)
        array_push( $ret['options'], explode('=', $option, 2) );
        else
        array_push( $ret['options'], $option );
         
        continue;
      }
  
      // Is it a flag or a serial of flags? (prefixed with -)
      if ( substr( $arg, 0, 1 ) === '-' ) {
        for ($i = 1; isset($arg[$i]) ; $i++)
        $ret['flags'][] = $arg[$i];
  
        continue;
      }
  
      // finally, it is not option, nor flag
      $ret['arguments'][] = $arg;
      continue;
    }
    return $ret;
  }
  
  /**
  * ./script.php arg3
  * input : arg3 ; return true
  * input : arg4 ; return false
  * @param unknown_type $argument
  * @return bool
  */
  function getArg($arg){
    $args = $this::parseArgs();
    if(in_array($arg, $args)){
      return true;
    }else{
      return false;
    }
  }
  
  /**
   * Checks if a certain option is set and returns the string.
   * ./script --opt1
   * @param unknown_type $option
   */
  function getOption($option){
    //$args = $this->parseArgs();
    //return $args['options'][$option];
  }
  
  
  
  
  /**
   * Handle the default help flag
   */
  private function flag_h($opt = null){
    if($opt == 'help'){
      return 'Display help.';
    }
    $this->help();
  }
  
  /**
   * Handle the default help argument
   */
  private function argument_help($opt = null){
    if($opt == 'help'){
      return 'Display help.';
    }
    $this->help();
  }
  
  /**
   * ./script.php --option1
   *  --option1=var1 =>
   *    array('options' =>
   *      array( 0 => 'option1',
   *        array('0' => 'option1', '1' => 'var1'))
   * 
   * @param unknown_type $opt
   */
  private function option_help($opt = null){
    
    if($opt == 'help'){
      return 'Display help for a specific command. ?=command';
    }
    
    
    if(substr($opt, 0, 2) == '--'){
      $opt = str_replace('--', '', $opt);
      //option
      $method = 'option_'.$opt;
      
    }elseif(substr($opt, 0, 1) == '-'){
      
      //flag
      $opt = str_replace('-', '', $opt);
      $method = 'flag_'.$opt;
       
    }else{
      //argument
      $method = 'argument_'.$opt;
    }
    
    
    if(method_exists($this, $method)){
      print "\n".$this->$method('help')."\n\n";
    }elseif($opt == null){
      $this->help();
    }else{
      print "\n".'Option, argument or flag not found.'."\n\n";
    }
    
  }
  

  
  /**
   * Print out help for this program.
   * The help is auto generated using various variables.
   */
  public function help($args = array()){
    print $this->colorText($this->appname, "LIGHT_RED")."\n";
    print $this->colorText($this->author.' - '.$this->copyright, "LIGHT_RED")."\n";
        
    
    for($i=0; $i < strlen($this->appname.$this->author.$this->copyright); $i++){ print '-'; }
    print "\n";
    
    $methods = get_class_methods(get_class($this));
    foreach($methods as $method){
      if(substr($method,0, 4) == 'flag'){
        $flag = str_replace('flag_', '', $method);
        $flags_help[$flag] = $this->$method('help');
      }elseif(substr($method,0, 8) == 'argument'){
        $argument = str_replace('argument_', '', $method);
        $arguments_help[$argument] = $this->$method('help');
      }elseif(substr($method,0, 6) == 'option'){
        $option = str_replace('option_', '', $method);        
        $options_help[$option] = $this->$method('help');
      }
    }
    
    print $this->colorText(' Flags:', "BLUE")."\n";
    foreach($flags_help as $flag => $desc){
      $spaces = 20 - strlen($flag);
      printf("  -%s%".$spaces."s%s\n", $flag,'', $desc);      
    }
    print "\n".$this->colorText(' Arguments:', "BLUE")."\n";
    foreach($arguments_help as $arg => $desc){
      $spaces = 20 - strlen($arg) + 1;

      printf("  %s%".$spaces."s%s\n", $arg,'', $desc);      
    }
    print "\n".$this->colorText(' Options:', "BLUE")."\n";
    foreach($options_help as $opt => $desc){
      $spaces = 20 - strlen($opt) - strlen($opt) - 4;
      printf("  --%s;%s=?%".$spaces."s%s\n",$opt, $opt,'', $desc);
    }
    print "\n";
    exit();
  }
  
  
  # first define colors to use
  private $_colors = array(
    "LIGHT_RED"     => "[1;31m",
    "LIGHT_GREEN"   => "[1;32m",
    "YELLOW"        => "[1;33m",
    "LIGHT_BLUE"    => "[1;34m",
    "MAGENTA"       => "[1;35m",
    "LIGHT_CYAN"    => "[1;36m",
    "WHITE"         => "[1;37m",
    "NORMAL"        => "[0m",
    "BLACK"         => "[0;30m",
    "RED"           => "[0;31m",
    "GREEN"         => "[0;32m",
    "BROWN"         => "[0;33m",
    "BLUE"          => "[0;34m",
    "CYAN"          => "[0;36m",
    "BOLD"          => "[1m",
    "UNDERSCORE"    => "[4m",
    "REVERSE"       => "[7m",
    
  );

  
  /**
   * Output coloized text to the terminal
   */
  function colorText($text, $color="NORMAL", $back=1){
    $out = $this->_colors[$color];
    if($out == ""){
      $out = "[0m";
    }
    if($back){
      return chr(27)."$out$text".chr(27)."[0m";#.chr(27);
    }else{
      echo chr(27)."$out$text".chr(27).chr(27)."[0m";#.chr(27);
    }
  }
  
}

