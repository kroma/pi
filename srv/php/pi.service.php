<?

    /**
     *  Pi Service base class
     *
     *  Implements basic functions that other Pi 
     *  services will need.
     *
     *
     * @author 2011-2014 Johan Telstad <jt@viewshq.no>
     * 
     */

    require_once('pi.php');


    class PiService extends Pi {

      private $name = 'service';

      public function __construct() {
      }


    }


?>