  <?php


    define('PI_ROOT', '/home/kroma/dev/www/pi/srv/php/');
    require_once PI_ROOT."pi.config.php";
    require_once PI_ROOT."pi.exception.class.php";
    require_once PI_ROOT."pi.functions.php";

  	require_once("websocket.server.php");


    error_reporting(E_ALL);

    class TimeHandler extends WebSocketUriHandler{

      private $user = null;

      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

          parent::say("[TIME] {$msg->getData()}");
          if($this->user === null){
            $this->user = $user;
          }
          $user->sendMessage(WebSocketMessage::create(json_encode(array("time"=> microtime(true)))));
      }


      public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $obj){
          $this->say("[TIME] Admin TEST received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
      }
    }


    class EchoHandler extends WebSocketUriHandler{

      public function say($msg=''){
        echo "$msg \r\n";
      }

      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
          $this->say("[ECHO] {$msg->getData()}");
          // Echo $msg back to user
          $user->sendMessage($msg);
      }

      public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $obj){
          $this->say("[ECHO] Admin TEST received!");

          $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
          $user->sendFrame($frame);
      }
    }


    /**
     * The Pi session handler, hands off session requests to worker script,
     * allocates session id as incremental port number between 8100-8999
     *
     */

    class AppSessionHandler extends WebSocketUriHandler{

      private $myclient           = null;
      private $currentSessionPort = 0;
      private $currentSessionId   = 0;
   
      protected function reply($request, $message="", $status = 0, $event='reply'){
        $json = json_encode(array('OK'=>$status, 'message'=>$message, "event"=>$event, "request"=>$request));
        $this->myclient->sendMessage(WebSocketMessage::create($json));
      }


      protected function fork($script){
        $pid    = pcntl_fork();
        $env    = array();
        $params = array($script);

        // set up child process
        $env['parent_pid']    = getmypid();
        $env['parent_script'] = __FILE__;
        $env['session_port']  = $this->currentSessionPort;
        $env['session_id']    = $_SESSION['id'];
        $env['session']    = $_SESSION;
        
        if ($pid === -1) {
             return false;
        } else if ($pid) {
             // we are the parent
             $this->say("Parent: started child process with pid ".$pid);
             return $pid;
        } else {
          $this->say("Child: starting with pid ".getmypid().". \nScript: $script");
          pcntl_exec("/usr/bin/php",$params,$env);
          print("exec failed!");
          exit(0);
             // we are the child
        }
      }

      protected function newSession(){
        // next session is +1 
        $this->currentSessionPort++;
 
        // kind of dirty, but will do for now
        // we should really keep count in Redis, or something similar
        try{
          if (!file_exists(SESSION_SCRIPT)){
            throw new PiException("Session handler script does not exist: ". SESSION_SCRIPT, 1);
          }
          if(false === ($result = $this->fork(SESSION_SCRIPT))){
            throw new PiException("Fork failed", 1);
          }
        }
        catch(Exception $e){
          print(get_class($e).": ".$e->getMessage());
          //reset session count
          $this->currentSessionPort--;
          return false;
        }
        //calculate session port
        return 8100 + ($this->currentSessionPort % 900);
      }


      protected function sendData($data=null, $event='session'){
        $json = json_encode(array('content'=>$data, "event"=>$event));
        $this->myclient->sendMessage(WebSocketMessage::create($json));
      }


      public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
        if($this->myclient===null){
          $this->myclient = $user;
        }
        $message = json_decode($msg->getData(), true);
        if(!isset($message['command'])){
          // reply($request, $message="", $status = 0, $event='info'){
          $this->reply(print_r($msg,true), "No command, expected 'session'. Also, remember that keyword 'command' should always be lowercase", 0, "error");
          print('[SESSION] => '.print_r($msg, true));  
          return;
        }
        switch (strtolower($message['command'])) {
          case 'session':
            $sessionport  = $this->newSession();
            $success      = ($sessionport !== false);
            $this->sendData(array("OK"=>$success, "SessionPort"=>$sessionport, "time"=>time()));
            break;
          default:
            $this->reply($message, "Expecting the 'session' command.");
            break;
        }
      }
    }


    /**
     * The pi application server
     *
     * @author Johan Telstad, jt@kroma.no
     *
     */

    class PiServer implements IWebSocketServerObserver{
        protected $debug = true;
        protected $server = null;
        //private 	$redis 	= null;

        public function __construct(){
            $this->server = new WebSocketServer('tcp://0.0.0.0:8000', 'secretkey');
            $this->server->addObserver($this);

            $this->server->addUriHandler("session", new AppSessionHandler());
            $this->server->addUriHandler("time",    new TimeHandler());
            $this->server->addUriHandler("echo",    new EchoHandler());
        }


        public function onConnect(IWebSocketConnection $user){
            $this->say("[SERVER]{userid:{$user->getId()}, event: \"connect\", message: \"Welcome.\"}");
        }


        public function onMessage(IWebSocketConnection $user, IWebSocketMessage $msg){

            $this->say("[SERVER] {$user->getId()} says '{$msg->getData()}'");
        }


        public function onDisconnect(IWebSocketConnection $user){
            $this->say("[SERVER] {$user->getId()} disconnected");
        }


        public function onAdminMessage(IWebSocketConnection $user, IWebSocketMessage $msg){
            $this->say("[SERVER] Admin Message received!");

            $frame = WebSocketFrame::create(WebSocketOpcode::PongFrame);
            $user->sendFrame($frame);
        }


        public function say($msg=''){
            echo "$msg \r\n";
        }


        public function run(){
      		print("running\n");
          try{
            $this->server->run();
            $this->say(APP_NAME." ".APP_VERSION);
            $this->say("\trunning on ". APP_PLATFORM);
            $this->say("========================================");
            $this->say("Server Started : " . date('Y-m-d H:i:s'));
            $this->say("Listening on   : " . $this->_url);
            $this->say("========================================");
          }
          catch(Exception $e){
            print("ERROR: " .get_class($e). " -> " .$e->getMessage());
          }
        }
    }

  $server = new PiServer();
  $server->run();

?>