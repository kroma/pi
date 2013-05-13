/**
 * Minimal app bootstrapper for internal purposes
 *
 * Merges existing app object with new
 *
 */


    π.TMP = π.app;

    π.app = {

      APP_ROOT  : π.APP_ROOT,
      LIB_ROOT  : π.LIB_ROOT,

      self      : this,


      __init : function() {
        for(var key in pi.TMP) {
          this[key] = pi.TMP[key]
        }
        pi.TMP = null;
      },
    };

    π.app.__init();

π.require('events', false);    
