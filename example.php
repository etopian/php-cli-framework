#!/usr/bin/php
<?php
include "cli.php";


class Example extends CLI{

  function __construct($appname = null, $author = null, $copyright = null) {
    parent::__construct('CLI Framework Example', 'Author Name', '(c) 2012 Etopian Inc.');
  }

  /**
   * You are responsible for defining variables (class attributes) which will be used by
   * $this->main() to do its job.
   *
   * You can use arguments, flags, and options functions (methods) explained later 
   * to define these variables (class attributes).
   * First they are set using args, flags, and options functions, and then main() is called.
   */
  private $optionvar1 = 'example opt var';
  private $flagvar2 = 'bar';
  private $argumentvar = 'argvar';
  
  /**
   * The main() function gets called if at least one argument is present.
   * If no arguments are present, the automatically generated help is displayed.
   *
   * The main functions job to do the main work of the script.
   * 
   */
  public function main(){

    //how to use the $this->getInput() function to get specific input from the user.
    $input = '';
    while($input != 'yes'){
      $input = $this->getInput('Type yes to continue. Type no to exit.');
      if($input == 'no'){
        exit();
      }
    }
  }
  
  
  /**
   * Now we define flags, arguments and options.
   * Notice each one of the defintions for these functions must be public.
   *
   *  The basic naming convention is:
   *    public function flag_NAME
   *    public function option_NAME
   *    public function argument_NAME
   *
   * In the function we define the help using:
   *  if($opt == 'help'){  return 'help message'. }
   *
   * We then follow up code to process that argument, flag, or option.
   *
   * There are no return values expected from any of the following functions.
   * 
   */
  
  
  /**
   * Define the flag -e, so if you run './example -e' this function will be called
   * Flags do not handle values, to handle values ($opt) use option_ for that.
   */
  public function flag_e($opt = null){
    if($opt == 'help'){
      return 'Help for the flag -e';
    }
    print "\n".'flag_e was just called and $opt was: '.$opt."\n";
  }
  
  /**
   * Argument is like flag, but just a string.
   * ./example.php example
   */
  public function argument_example($opt = null){
    if($opt == 'help'){
      return 'Help for the argument \'example\'';
    }
    
    print "\n".'argument_example was just called and $opt was: '.$opt."\n";
    $this->argumentvar = 'example';

  }
  
  /**
   * ./example.php --example=test
   *
   * Will output $opt = test when this function is called.
   *  
   */
  public function option_example($opt = null){    
    if($opt == 'help'){
      return 'Help for the option --example=value';
    }
    print "\n".'option_example was just called and $opt was: '.$opt."\n";
    $this->optionvar1 = $opt;
    
    //you can also call $this->getInput('message');
    //from within these functions to get input associated with the given option.
    
  } 

}

/**
 *IMPORTANT, instantiate your class! i.e. new Classname();
 */
new Example();
