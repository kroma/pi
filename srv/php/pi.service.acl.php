<?php

    /**
     * pi.service.acl
     * 
     * The pi ACL service, a server that  
     * maintains an Access Control List and
     * provides an access request (allowed/denied) method to other services
     * 
     * 
     * This is part of the core of the pi server
     *
     * @author Johan Telstad, jt@enfield.no, 2011-2013
     *
     */


    require_once("pi.service.php");


    class PiServiceACL extends PiService {

        private   $debug            = false;

        private   $subscribercount  = -1;
        private   $running          = false;
        private   $ticks            = -1;
        private   $ticklength       = 0;

        protected $address          = "";
        protected $name             = "";




        public function __construct() {
          $this->address    = basename(__FILE__, '.php');
          $this->name       = $this->address;
        }



        private function quit($msg="Goodbye. No message.") {

          die($msg);

        }




        public function run($dbg=false){

          if(!$this->__init($dbg)) {
            $this->quit("__init() returned false, aborting run().");
          }
          print("\nRunning : " . basename(__FILE__, '.php') . "\n");
        }

    }




  $admin = new PiServiceACL();

  try {
    $admin->run();
  }
  catch(Exception $e) {
    print(get_class($e) . ": " . $e->getMessage() . "\n");
  }


?>