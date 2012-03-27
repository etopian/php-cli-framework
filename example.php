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
   */
  private $optionvar1 = 1;
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


}

/**
 *IMPORTANT, instantiate your class! i.e. new Classname();
 */
new Example();
