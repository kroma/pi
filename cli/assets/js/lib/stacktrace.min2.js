(function(b,a){if(typeof exports==="object"){module.exports=a()}else{if(typeof define==="function"&&define.amd){define(a)}else{b.printStackTrace=a()}}}(this,function(){function a(c){c=c||{guess:true};var d=c.e||null,f=!!c.guess;var e=new a.implementation(),b=e.run(d);return(f)?e.guessAnonymousFunctions(b):b}a.implementation=function(){};a.implementation.prototype={run:function(b,c){b=b||this.createException();c=c||this.mode(b);if(c==="other"){return this.other(arguments.callee)}else{return this[c](b)}},createException:function(){try{this.undef()}catch(b){return b}},mode:function(b){if(b["arguments"]&&b.stack){return"chrome"}if(b.stack&&b.sourceURL){return"safari"}if(b.stack&&b.number){return"ie"}if(b.stack&&b.fileName){return"firefox"}if(b.message&&b["opera#sourceloc"]){if(!b.stacktrace){return"opera9"}if(b.message.indexOf("\n")>-1&&b.message.split("\n").length>b.stacktrace.split("\n").length){return"opera9"}return"opera10a"}if(b.message&&b.stack&&b.stacktrace){if(b.stacktrace.indexOf("called from line")<0){return"opera10b"}return"opera11"}if(b.stack&&!b.fileName){return"chrome"}return"other"},instrumentFunction:function(c,e,f){c=c||window;var b=c[e];c[e]=function d(){f.call(this,a().slice(4));return c[e]._instrumented.apply(this,arguments)};c[e]._instrumented=b},deinstrumentFunction:function(b,c){if(b[c].constructor===Function&&b[c]._instrumented&&b[c]._instrumented.constructor===Function){b[c]=b[c]._instrumented}},chrome:function(b){return(b.stack+"\n").replace(/^[\s\S]+?\s+at\s+/," at ").replace(/^\s+(at eval )?at\s+/gm,"").replace(/^([^\(]+?)([\n$])/gm,"{anonymous}() ($1)$2").replace(/^Object.<anonymous>\s*\(([^\)]+)\)/gm,"{anonymous}() ($1)").replace(/^(.+) \((.+)\)$/gm,"$1@$2").split("\n").slice(0,-1)},safari:function(b){return b.stack.replace(/\[native code\]\n/m,"").replace(/^(?=\w+Error\:).*$\n/m,"").replace(/^@/gm,"{anonymous}()@").split("\n")},ie:function(b){return b.stack.replace(/^\s*at\s+(.*)$/gm,"$1").replace(/^Anonymous function\s+/gm,"{anonymous}() ").replace(/^(.+)\s+\((.+)\)$/gm,"$1@$2").split("\n").slice(1)},firefox:function(b){return b.stack.replace(/(?:\n@:0)?\s+$/m,"").replace(/^(?:\((\S*)\))?@/gm,"{anonymous}($1)@").split("\n")},opera11:function(h){var b="{anonymous}",j=/^.*line (\d+), column (\d+)(?: in (.+))? in (\S+):$/;var l=h.stacktrace.split("\n"),m=[];for(var d=0,g=l.length;d<g;d+=2){var f=j.exec(l[d]);if(f){var k=f[4]+":"+f[1]+":"+f[2];var c=f[3]||"global code";c=c.replace(/<anonymous function: (\S+)>/,"$1").replace(/<anonymous function>/,b);m.push(c+"@"+k+" -- "+l[d+1].replace(/^\s+/,""))}}return m},opera10b:function(j){var h=/^(.*)@(.+):(\d+)$/;var d=j.stacktrace.split("\n"),c=[];for(var g=0,b=d.length;g<b;g++){var f=h.exec(d[g]);if(f){var k=f[1]?(f[1]+"()"):"global code";c.push(k+"@"+f[2]+":"+f[3])}}return c},opera10a:function(h){var b="{anonymous}",j=/Line (\d+).*script (?:in )?(\S+)(?:: In function (\S+))?$/i;var k=h.stacktrace.split("\n"),l=[];for(var d=0,g=k.length;d<g;d+=2){var f=j.exec(k[d]);if(f){var c=f[3]||b;l.push(c+"()@"+f[2]+":"+f[1]+" -- "+k[d+1].replace(/^\s+/,""))}}return l},opera9:function(k){var f="{anonymous}",j=/Line (\d+).*script (?:in )?(\S+)/i;var d=k.message.split("\n"),c=[];for(var h=2,b=d.length;h<b;h+=2){var g=j.exec(d[h]);if(g){c.push(f+"()@"+g[2]+":"+g[1]+" -- "+d[h+1].replace(/^\s+/,""))}}return c},other:function(k){var b="{anonymous}",i=/function\s*([\w\-$]+)?\s*\(/i,h=[],g,f,d=10;var j=Array.prototype.slice;while(k&&k["arguments"]&&h.length<d){g=i.test(k.toString())?RegExp.$1||b:b;f=j.call(k["arguments"]||[]);h[h.length]=g+"("+this.stringifyArguments(f)+")";try{k=k.caller}catch(c){h[h.length]=""+c;break}}return h},stringifyArguments:function(d){var c=[];var f=Array.prototype.slice;for(var e=0;e<d.length;++e){var b=d[e];if(b===undefined){c[e]="undefined"}else{if(b===null){c[e]="null"}else{if(b.constructor){if(b.constructor===Array){if(b.length<3){c[e]="["+this.stringifyArguments(b)+"]"}else{c[e]="["+this.stringifyArguments(f.call(b,0,1))+"..."+this.stringifyArguments(f.call(b,-1))+"]"}}else{if(b.constructor===Object){c[e]="#object"}else{if(b.constructor===Function){c[e]="#function"}else{if(b.constructor===String){c[e]='"'+b+'"'}else{if(b.constructor===Number){c[e]=b}else{c[e]="?"}}}}}}}}}return c.join(",")},sourceCache:{},ajax:function(b){var c=this.createXMLHTTPObject();if(c){try{c.open("GET",b,false);c.send(null);return c.responseText}catch(d){}}return""},createXMLHTTPObject:function(){var d,b=[function(){return new XMLHttpRequest()},function(){return new ActiveXObject("Msxml2.XMLHTTP")},function(){return new ActiveXObject("Msxml3.XMLHTTP")},function(){return new ActiveXObject("Microsoft.XMLHTTP")}];for(var c=0;c<b.length;c++){try{d=b[c]();this.createXMLHTTPObject=b[c];return d}catch(f){}}},isSameDomain:function(b){return typeof location!=="undefined"&&b.indexOf(location.hostname)!==-1},getSource:function(b){if(!(b in this.sourceCache)){this.sourceCache[b]=this.ajax(b).split("\n")}return this.sourceCache[b]},guessAnonymousFunctions:function(l){for(var h=0;h<l.length;++h){var g=/\{anonymous\}\(.*\)@(.*)/,n=/^(.*?)(?::(\d+))(?::(\d+))?(?: -- .+)?$/,c=l[h],d=g.exec(c);if(d){var f=n.exec(d[1]);if(f){var e=f[1],b=f[2],k=f[3]||0;if(e&&this.isSameDomain(e)&&b){var j=this.guessAnonymousFunction(e,b,k);l[h]=c.replace("{anonymous}",j)}}}}return l},guessAnonymousFunction:function(d,g,b){var c;try{c=this.findFunctionName(this.getSource(d),g)}catch(f){c="getSource failed with url: "+d+", exception: "+f.toString()}return c},findFunctionName:function(b,f){var h=/function\s+([^(]*?)\s*\(([^)]*)\)/;var l=/['"]?([$_A-Za-z][$_A-Za-z0-9]*)['"]?\s*[:=]\s*function\b/;var j=/['"]?([$_A-Za-z][$_A-Za-z0-9]*)['"]?\s*[:=]\s*(?:eval|new Function)\b/;var c="",n,k=Math.min(f,20),e,d;for(var g=0;g<k;++g){n=b[f-g-1];d=n.indexOf("//");if(d>=0){n=n.substr(0,d)}if(n){c=n+c;e=l.exec(c);if(e&&e[1]){return e[1]}e=h.exec(c);if(e&&e[1]){return e[1]}e=j.exec(c);if(e&&e[1]){return e[1]}}}return"(?)"}};return a}));