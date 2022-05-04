
// version: 1

// ザガタ。六 /////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////

function zXML(za,a,n) {
	/* Zagata.XSLT */

	this.za = (typeof(za)=='undefined')?false:za; // core
	var a = (typeof(a)=='undefined')?false:a; // attr
	this.n = (typeof(n)=='undefined')?'zXML':n; // name

	this.x = false; // db
	this.load = function() {
		console.log('love xml loaded');
		this.x = this.x2a( this.za.m['ax'].x.firstElementChild );

		var i=0, ix=this.x.length;
		for(i;i<ix;i++) {
			// console.log(this.x[i].ca);
			this.za.mm(false, this.x[i]);
		}
		// console.log(this.x);
		
		this.za.msg('ntf','db', 'love.xml loaded');
		this.za.ee(this.n+'_ready');
	};

	this.qq = function(k,v) {
		var v = (typeof(v)=='undefined')?false:v;
		if(k=='del'&&v) { // del
			void(0);
		} else { if(k && v) { // red
			void(0);
		} else { if(k) { // get
			var re = new Array();
			var y = 0, iy = this.x.length, i = false;
			for(y;y<iy;y++) {
				i = this.x[y];
				if( k.indexOf(',')>=0 && !i.hasOwnProperty('idx') && i.hasOwnProperty('ids') && i['ids'].indexOf(k)===0 ) {
					re.push(i);
				} else { if(k=='*' && i.hasOwnProperty('idx') && i['idx']==k) {
					re.push(i);
				} else { if(typeof(k)=='number' && i.hasOwnProperty('id') && i['id']==k) {
					re.push(i);
					break;
				} else { if(typeof(k)=='string' && i.hasOwnProperty('ca') && i['ca']==k) {
					re.push(i);
				} else { if(typeof(k)=='object' && i.hasOwnProperty('ca') && i['ca']==k[0]) {
					re.push(i[k1]);
					break;
				} else { } } } } }
			}
			// return ((is_array($re)&&count($re)>0)||!is_array($re))?$re:false;
			return ((typeof(re)=='object' && re.length>0) || typeof(re)!='object')?re:false;
		} else { if(v) { // add
			void(0);
		} else {
			this.za.msg('err','zXML','no condition accepted by qq: '+k);
		} } } }
		return this;	
	};
	
	///////////////////////////////
	// funcs
	this.x2a = function(i) {
		var re = new Array();
		if(this.msie) {
			if(i.nodeType!=8) {
				var y=0, tmp;
				if(i.hasChildNodes()) {
					for(y;y<i.childNodes.length;y++) {
						if(i.childNodes.item(y).nodeType!=8) {
							if(i.childNodes.item(y).childNodes.length==1 && i.childNodes.item(y).childNodes.item(0).nodeType==3) { 
								tmp = i.childNodes.item(y).childNodes.item(0).nodeValue; 
							} else { tmp = this.x2a(i.childNodes.item(y)); }
							
							if(i.childNodes.item(y).nodeType == 4) { re = tmp; // #cdata always text
							} else { if(i.childNodes.item(y).nodeType != 3) { // #text always empty
									if(i.childNodes.item(y).nodeName!='i') { re[i.childNodes.item(y).nodeName] = tmp; } else { re.push(tmp); }
							} else {} }
						} else {}
					}
				} else { re = i.nodeValue; }
			} else {}
		} else {
			if(i.children.length>0) {
				var y=0, tmp;
				for(y;y<i.children.length;y++) {
					tmp = (i.children.item(y).children.length>0)?this.x2a(i.children.item(y)):i.children.item(y).textContent;
					if(i.children.item(y).nodeName!='i') { re[i.children.item(y).nodeName] = tmp; } else { re.push(tmp); }
				}
				y=null; tmp=null;
			} else { re = i.textContent; }
		}
		i = null; 
	return re;
	};
	
	///////////////////////////////
	// ini
	this.za.e['ax'] = new Array();
	this.za.ee('ax_con',new Array(this,'load'));
	this.za.m['ax'].upd('/za/love1.xml');
	za.msg('ntf','db', 'start loading love.xml');
};

////////////////////////////////////////////////////////////////
if(typeof(zlo)=='object') {
	zlo.da('zXML');
} else {
  console.log('zXML');
}
