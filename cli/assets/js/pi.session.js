// ensure we have a global app object

  if(!pi.app) {

    //ensure we have loaded the app module first
    pi.require('app');
  }

/**
 *  Session object. This is where we have to debug the pants off of this library
 *  This bit should be bulletproof
 *
 *  We need to implement a feedback mechanism for js errors that we can trap
 *  and possibly for exceptions
 *  could be as easy as a json webservice
 *
 *  That way we can monitor apps in the wild and pick up on problems quickly
 * 
 */

  π.app.session = {

    __socket         : null,
    __initsocket     : null,
    __initialized    : false,

    //default values, override by passing options object to start() function
    __sessionserver  : window.location.hostname,
    __initport       : 8000,
    __inituri        : '/session',
    __sessionport    : 8101,
    __sessionuri     : '',
    that             : this,
    self             : this,


    __init : function (DEBUG) {

      var 
        patharray = window.location.pathname.split("/"),
        host      = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__inituri,
        DBG       = DEBUG || false;

      if(this.__initialized === true){
        //something is not right
        pi.log("error: __init() called twice ");
        return false;
      }

      if(DBG){
        // go straight to session
//        host = 'ws://' + this.__sessionserver + ':' + this.__initport + this.__sessionuri;
        return this.__startSession(host);
      }
      try {
        this.__initsocket = this.__createSocket(host);
        pi.log('WebSocket - status ' + this.__initsocket.readyState);

        this.__initsocket.addEventListener('open', function(event) {
          // handle open event
          var init__command = {command: 'session'};

          pi.log('Opened WebSocket. This: ' + this + ', that: ' + that);
          self.send(JSON.stringify(init__command));
        });

        this.__initsocket.addEventListener('message', function(event) {
          // handle message event
          pi.log('Received (' + event.data.length + ' bytes): ' + event.data);
          self.__startSession();
          });

        this.__initsocket.addEventListener('close', function(event) {
          // handle close event
          pi.log('Disconnected. Status -> ' + this.readyState);
        });

        this.__initsocket.addEventListener('error', function(event) {
          // handle close event
          pi.log('ERROR! -> ', event);
        });

      this.__initialized = true;
      return true;
      }
      catch (ex) {
        pi.log(ex);
        this.log(ex);
        return false;
      }
    },

    __handleError  : function(msg){
  //    alert('__handleError: ' + msg);
      pi.log('error: ' + msg);
    },

    send : function (obj) {
      try {
        var msg = JSON.stringify(obj);
        this.__socket.send(msg);
        pi.log('Sent (' + msg.length + ' bytes): ' + msg);
        this.log('Sent (' + msg.length + ' bytes): ' + msg);
      }
      catch (ex) {
        this.log(ex);
      }
    },

    quit : function () {
      this.log('Goodbye!');
      this.__socket.close();
      this.__socket = null;
      this.__initsocket.close();
      this.__initsocket = null;
    },

    __createSocket : function (host) {
      if (window.WebSocket){
        return new WebSocket(host);
      }
      else if (window.MozWebSocket){
        return new MozWebSocket(host);
      }
      else{
        return false;
      }
    },


    __startSession : function (host) {
      var that = this;
      try {
        this.log('Connecting: ' + host);
        this.__sessionsocket = this.__createSocket(host);
        this.log('SessionSocket - status ' + this.__sessionsocket.readyState);
        this.__sessionsocket.addEventListener('open', function(event) {
          pi.log('Opened SessionSocket. Event: ', event);
          that.send(JSON.stringify({command: 'sessionstart'}));
        });
        this.__sessionsocket.addEventListener('message', function(event) {

          // handle message event
          pi.log('Received (' + event.data.length + ' bytes): ' + event.data);
          });
        this.__sessionsocket.addEventListener('close', function(event) {

          // handle close event
          pi.log('Session closed. Status -> ' + this.readyState);
        });
      return true;
      }
      catch (ex) {
        pi.log(ex);
        return false;
      }
    },


    start : function (DEBUG) {
      if( !this.__init(DEBUG) ) {
        pi.log('__init() returned false, aborting...');
      }
    }
  };
