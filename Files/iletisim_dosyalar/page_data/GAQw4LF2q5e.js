if (self.CavalryLogger) { CavalryLogger.start_js(["izS+O"]); }

__d("DesktopHscrollUnitEventConstants",[],(function a(b,c,d,e,f,g){f.exports={HSCROLL_ITEM_INSERTED_EVENT:"DesktopHScrollUnit/itemInserted",HSCROLL_ITEM_SHOWN_EVENT:"DesktopHScrollUnit/itemShown",HSCROLL_ITEM_HIDE_EVENT:"DesktopHScrollUnit/HideIndividualItem",HSCROLL_ITEM_SCROLL_BEFORE_XOUT_EVENT:"DesktopHScrollUnit/scrollItemBeforeXout",HSCROLL_ITEM_UNHIDE_EVENT:"DesktopHScrollUnit/unhideIndividualItem",HSCROLL_LAST_ITEM_NFX_ACTION_TAKEN:"logLastAdXout",HSCROLL_PAGER_ITEM_HIDE_EVENT:"onXoutIndividualItem"}}),null);
__d("collectDataAttributes",["DataAttributeUtils","getContextualParent"],(function a(b,c,d,e,f,g){var h="normal";function i(j,k,l){var m={},n=[],o=k.length,p;for(p=0;p<o;++p){m[k[p]]={};n.push("data-"+k[p])}if(l){m[h]={};for(p=0;p<(l||[]).length;++p)n.push(l[p])}var q={tn:"","tn-debug":","};while(j){if(j.getAttribute)for(p=0;p<n.length;++p){var r=n[p],s=c("DataAttributeUtils").getDataAttribute(j,r);if(s){if(p>=o){if(m[h][r]===undefined)m[h][r]=s;continue}var t=JSON.parse(s);for(var u in t)if(q[u]!==undefined){if(m[k[p]][u]===undefined)m[k[p]][u]=[];m[k[p]][u].push(t[u])}else if(m[k[p]][u]===undefined)m[k[p]][u]=t[u]}}j=c("getContextualParent")(j)}for(var v in m)for(var w in q)if(m[v][w]!==undefined)m[v][w]=m[v][w].join(q[w]);return m}f.exports=i}),null);
__d("FeedTrackingAsync",["Arbiter","Run","collectDataAttributes"],(function a(b,c,d,e,f,g){var h;f.exports.init=function(){if(h)return;h=c("Arbiter").subscribe("AsyncRequest/send",function(i,j){var k=j.request,l=k.getRelativeTo();if(l){var m=k.getData(),n=c("collectDataAttributes")(l,["ft"]);if(n.ft&&Object.keys(n.ft).length)Object.assign(m,n)}});c("Run").onLeave(function(){h.unsubscribe();h=null})}}),null);
__d("AsyncFormRequestUtils",["Arbiter"],(function a(b,c,d,e,f,g){var h={subscribe:function i(j,k,l){c("Arbiter").subscribe("AsyncRequest/"+k,function(m,n){var o=n.request.relativeTo;if(o&&o===j)l(n)})}};f.exports=h}),null);
__d("EmbeddedPostPluginActionTypes",[],(function a(b,c,d,e,f,g){f.exports=Object.freeze({CLICK:"click"})}),null);
__d("EmbeddedPostPluginActions",[],(function a(b,c,d,e,f,g){f.exports=Object.freeze({EMBEDDED_POSTS_COMMENT:"embedded_post_comment",EMBEDDED_POSTS_LIKE:"embedded_post_like",EMBEDDED_POSTS_SHARE:"embedded_post_share",EMBEDDED_POSTS_UNLIKE:"embedded_post_unlike"})}),null);
__d("XPostPluginLoggingController",["XController"],(function a(b,c,d,e,f,g){f.exports=c("XController").create("/platform/plugin/post/logging/",{})}),null);
__d("PluginFeedFooterActionLogger",["AsyncRequest","DOM","EmbeddedPostPluginActions","EmbeddedPostPluginActionTypes","Event","XPostPluginLoggingController"],(function a(b,c,d,e,f,g){var h={initializeClickLoggers:function i(j,k,l,m,n,o,p,q,r){var s=function s(t,u){try{var v=c("DOM").find(j,"."+t);c("Event").listen(v,"click",function(x){new(c("AsyncRequest"))().setURI(c("XPostPluginLoggingController").getURIBuilder().getURI()).setData({action:u,action_type:c("EmbeddedPostPluginActionTypes").CLICK,source:o,story_token:p,referer_url:q,is_sdk:r}).send()})}catch(w){}};s(k,c("EmbeddedPostPluginActions").EMBEDDED_POSTS_LIKE);s(l,c("EmbeddedPostPluginActions").EMBEDDED_POSTS_UNLIKE);s(m,c("EmbeddedPostPluginActions").EMBEDDED_POSTS_COMMENT);s(n,c("EmbeddedPostPluginActions").EMBEDDED_POSTS_SHARE)}};f.exports=h}),null);
__d("randomInt",["invariant"],(function a(b,c,d,e,f,g,h){function i(j,k){var l=arguments.length;l>0&&l<=2||h(0);if(l===1){k=j;j=0}k=k;k>j||h(0);var m=this.random||Math.random;return Math.floor(j+m()*(k-j))}f.exports=i}),null);
__d("ClientIDs",["randomInt"],(function a(b,c,d,e,f,g){var h={},i={getNewClientID:function j(){var k=Date.now(),l=k+":"+(c("randomInt")(0,4294967295)+1);h[l]=true;return l},isExistingClientID:function j(k){return!!h[k]}};f.exports=i}),null);
__d("FBFeedLocations",[],(function a(b,c,d,e,f,g){f.exports=Object.freeze({NEWSFEED:1,GROUP:2,GROUP_PERMALINK:3,COMMUNITY:4,PERMALINK:5,SHARE_OVERLAY:6,PERMALINK_STREAM:7,GROUP_PINNED:8,FRIEND_LIST:9,TIMELINE:10,HASHTAG_FEED:11,TOPIC_FEED:12,PAGE:13,NOTIFICATION_FEED:14,GROUP_REPORTED:15,GROUP_PENDING:16,PAGES_FEED_IN_PAGES_MANAGER:17,TICKER_CLASSIC:18,PAGES_SUGGESTED_FEED_IN_PAGES_MANAGER:19,SEARCH:20,GROUP_SEARCH:21,NO_ATTACHMENT:22,EMBED:23,EMBED_FEED:24,ATTACHMENT_PREVIEW:25,STORIES_TO_SHARE:26,PROMPT_PERMALINK:27,TREND_HOVERCARD:28,OPEN_GRAPH_PREVIEW:30,HOTPOST_IN_PAGES_FEED:31,SCHEDULED_POSTS:32,TIMELINE_NOTES:33,PAGE_INSIGHTS:34,COMMENT_ATTACHMENT:35,PAGE_TIMELINE:36,GOODWILL_THROWBACK_PERMALINK:37,LIKE_CONFIRM:39,GOODWILL_THROWBACK_PROMOTION:40,BROWSE_CONSOLE:42,GROUP_FOR_SALE_COMPACT:43,ENTITY_FEED:44,GROUP_FOR_SALE_DISCUSSION:45,PAGES_CONTENT_TAB_PREVIEW:46,GOODWILL_THROWBACK_SHARE:47,TIMELINE_VIDEO_SHARES:48,EVENT:49,PAGE_PLUGIN:50,SRT:51,PAGES_CONTENT_TAB_INSIGHTS:52,ADS_PE_CONTENT_TAB_INSIGHTS:53,PAGE_ACTIVITY_FEED:54,VIDEO_CHANNEL:55,POST_TO_PAGE:56,GROUPS_GSYM_HOVERCARD:57,GROUP_POST_TOPIC_FEED:58,FEED_SURVEY:59,PAGES_MODERATION:60,SAVED_DASHBOARD:61,PULSE_SEARCH:62,GROUP_NUX:63,GROUP_NUX_POST_VIEW:64,EVENT_PERMALINK:65,FUNDRAISER_PAGE:66,EXPLORE_FEED:67,CRT:68,REVIEWS_FEED:69,VIDEO_HOME_CHANNEL:70,NEWS:71,TIMELINE_FRIENDSHIP:72,SAVED_REMINDERS:73,PSYM:74,ADMIN_FEED:75,CAMPFIRE_NOTE:76,PAGES_CONTEXT_CARD:77,ACTIVITY_LOG:78,WALL_POST_REPORT:79,TIMELINE_BREAKUP:80,POLITICIANS_FEED:81,PRODUCT_DETAILS:82,SPORTS_PLAY_FEED:83,GROUP_TOP_STORIES:84,PAGE_TIMELINE_PERMALINK:86,OFFERS_WALLET:87,INSTREAM_VIDEO_IN_LIVE:88,SPOTLIGHT:89,SEARCH_DERP:90,SOCIAL_BALLOT:91,GROUP_SUGGESTIONS_WITH_STORY:92,SOCIAL_BALLOT_PERMALINK:93,LOCAL_SERP:94,FUNDRAISER_SELF_DONATION_FEED:95,CONVERSATION_NUB:97,GROUP_TOP_SALE_STORIES:98,GROUP_LEARNING_COURSE_UNIT_FEED:99,SUPPORT_INBOX_READ_TIME_BLOCK:100,PAGE_POSTS_CARD:101,CRISIS_POST:102,SUPPORT_INBOX_GROUP_RESPONSIBLE:103,PAGE_POST_DIALOG:104,CRISIS_DIALOG_POST:105,PAGE_LIVE_VIDEOS_CARD:106,PAGE_POSTS_CARD_COMPACT:107,GROUP_MEMBER_BIO_FEED:108,LIVE_COMMENT_ATTACHMENT:109,GROUP_COMPOSER:110,GROUP_INBOX_GROUP:111,GROUP_INBOX_AGGREGATED:112,ENDORSEMENTS:113,EVENTS_DASHBOARD:114,CURATED_COLLECTIONS_PAGE:115,OYML:116,COLLEGE_HOMEPAGE:117,GROUP_LIVE_VIDEOS_CARD:118,COLLEGE_HIGHLIGHTS:119,VIDEO_PERMALINK:120,JOB_CAROUSEL_NETEGO:121,TOPIC_PAGE:122,USER_SCHEDULED_POSTS:123,GOODWILL_THROWBACK_ATTACHMENT_PREVIEW:124,INSTREAM_VIDEO_IN_WASLIVE:125,INSTREAM_VIDEO_IN_NONLIVE:126,SIGNAL_APP:127,ALBUM_FEED:128,TOP_MARKETPLACE_STORIES:129,CE_PII_STRIPPED_FEED:130,TAHOE:131,SAVE_COUNT_DIALOG:132,GROUP_POST_TAG_FEED:133,GOV_DIGEST:134,GROUP_SCHEDULED:135,GAMEROOM_FEED:136,WORKPLACE_HUB_PREVIEW:137,BRANDED_CONTENT_TRENDING_POSTS:138,GROUP_ANNOUNCEMENTS:139,GROUP_ANNOUNCEMENTS_FEED:140,EXTERN_CE_PII_STRIPPED_FEED:141,CRISIS_HUB_DESKTOP:142,GROUP_DRAFT_POSTS:143,TRENDING_ISSUES:144,GAME_HUB_FEED:145,LUMOS_POST_PREVIEW:146})}),null);
__d("PluginFeedLikeButton",["Arbiter","AsyncFormRequestUtils","ClientIDs","CSS","DOM","DOMEventListener","FBFeedLocations","FormSubmit","Keys","PluginOptin","URI"],(function a(b,c,d,e,f,g){window.resetConfirmStoryLike=function(i){c("CSS").show(c("DOM").find(document,"#likeStory_"+i));c("DOM").remove(c("DOM").find(document,"#confirmStory_"+i))};var h={addClientId:function i(j){j.setAttribute("value",c("ClientIDs").getNewClientID())},loggedOutLikeButton:function i(j,k,l){var m="";if(k===c("FBFeedLocations").EMBED)m="post";else if(k===c("FBFeedLocations").PAGE_PLUGIN)m="page";else throw new Error("Invalid FBFeedLocation type.");var n=new(c("PluginOptin"))(m).addReturnParams({act:"like_"+j});c("DOMEventListener").add(l,"click",n.start.bind(n))},init:function i(j,k,l,m,n){var o=function o(r){if(r.type==="keypress")if(r.keyCode===c("Keys").RETURN||r.keyCode===c("Keys").SPACE)c("FormSubmit").send(n);else return;c("FormSubmit").send(n)};c("DOMEventListener").add(k,"click",o);c("DOMEventListener").add(l,"click",o);c("DOMEventListener").add(k,"keypress",o);c("DOMEventListener").add(l,"keypress",o);var p=m.getAttribute("value")==="1";c("AsyncFormRequestUtils").subscribe(n,"send",function(r){var s=m.getAttribute("value")==="1";c("CSS").conditionShow(l,s);c("CSS").conditionShow(k,!s);c("Arbiter").inform("embeddedUfiToggle",{isLike:s,storyToken:j});m.setAttribute("value",s?"":"1")});c("AsyncFormRequestUtils").subscribe(n,"response",function(r){var s=r.response.payload;if(s&&!s.success){var t=s.isLike;c("CSS").conditionShow(k,t);c("CSS").conditionShow(l,!t);c("Arbiter").inform("revertLike",{isLike:t,storyToken:j});m.setAttribute("value",t?"1":"")}});var q=new(c("URI"))(window.location.search).getQueryData();if(p&&q.act==="like_"+j)c("FormSubmit").send(n)}};f.exports=h}),null);
__d("VideoDisplayTimePlayButton",["CSS","DataStore","Event"],(function a(b,c,d,e,f,g){var h={},i="_spinner",j={getClicked:function k(l){return c("DataStore").get(l,"clicked",false)},register:function k(l,m){var n=l.id;h[n]=c("Event").listen(l,"click",function(){if(m){c("CSS").hide(l);c("CSS").show(m)}c("DataStore").set(l,"clicked",true)});if(m)h[n+i]=c("Event").listen(m,"click",function(){if(m)c("CSS").hide(m);c("CSS").show(l);c("DataStore").set(l,"clicked",false)})},unregister:function k(l){var m=l.id;if(Object.prototype.hasOwnProperty.call(h,m))h[m].remove();var n=m+i;if(Object.prototype.hasOwnProperty.call(h,n))h[n].remove()}};f.exports=j}),null);
__d("VideosRenderingInstrumentation",["DataStore","VideoPlayerHTML5Experiments","performanceAbsoluteNow"],(function a(b,c,d,e,f,g){var h={storeRenderTime:function i(j){var k=c("VideoPlayerHTML5Experiments").useMonotonicallyIncreasingTimers?c("performanceAbsoluteNow")():Date.now();c("DataStore").set(j,"videos_rendering_instrumentation",k);return k},retrieveRenderTime:function i(j){var k=c("DataStore").get(j,"videos_rendering_instrumentation",NaN);if(Number.isNaN(k))k=h.storeRenderTime(j);return k}};f.exports=h}),null);
__d("CacheStorage",["ErrorUtils","EventListener","ExecutionEnvironment","FBJSON","WebStorage"],(function a(b,c,d,e,f,g){var h,i,j,k,l={memory:s,localstorage:q,sessionstorage:r},m="_@_",n="3b",o="CacheStorageVersion";function p(u){"use strict";this._store=u}p.prototype.getStore=function(){"use strict";return this._store};p.prototype.keys=function(){"use strict";var u=[];for(var v=0;v<this._store.length;v++)u.push(this._store.key(v));return u};p.prototype.get=function(u){"use strict";return this._store.getItem(u)};p.prototype.set=function(u,v){"use strict";this._store.setItem(u,v)};p.prototype.remove=function(u){"use strict";this._store.removeItem(u)};p.prototype.clear=function(){"use strict";this._store.clear()};h=babelHelpers.inherits(q,p);i=h&&h.prototype;function q(){"use strict";i.constructor.call(this,c("WebStorage").getLocalStorage())}q.available=function(){"use strict";return!!c("WebStorage").getLocalStorage()};j=babelHelpers.inherits(r,p);k=j&&j.prototype;function r(){"use strict";k.constructor.call(this,c("WebStorage").getSessionStorage())}r.available=function(){"use strict";return!!c("WebStorage").getSessionStorage()};function s(){"use strict";this._store={}}s.prototype.getStore=function(){"use strict";return this._store};s.prototype.keys=function(){"use strict";return Object.keys(this._store)};s.prototype.get=function(u){"use strict";if(this._store[u]===undefined)return null;return this._store[u]};s.prototype.set=function(u,v){"use strict";this._store[u]=v};s.prototype.remove=function(u){"use strict";if(u in this._store)delete this._store[u]};s.prototype.clear=function(){"use strict";this._store={}};s.available=function(){"use strict";return true};function t(u,v){"use strict";this._key_prefix=v||"_cs_";if(u=="AUTO"||!u)for(var w in l){var x=l[w];if(x.available()){u=w;break}}if(u)if(!l[u]||!l[u].available()){c("ExecutionEnvironment").canUseDOM;this._backend=new s()}else this._backend=new l[u]();var y=this.useBrowserStorage();if(y)c("EventListener").listen(window,"storage",this._onBrowserValueChanged.bind(this));var z=y?this._backend.getStore().getItem(o):this._backend.getStore()[o];if(z!==n)this.clear()}t.prototype.useBrowserStorage=function(){"use strict";return this._backend.getStore()===c("WebStorage").getLocalStorage()||this._backend.getStore()===c("WebStorage").getSessionStorage()};t.prototype.addValueChangeCallback=function(u){"use strict";this._changeCallbacks=this._changeCallbacks||[];this._changeCallbacks.push(u);return{remove:function(){this._changeCallbacks.slice(this._changeCallbacks.indexOf(u),1)}.bind(this)}};t.prototype._onBrowserValueChanged=function(u){"use strict";if(this._changeCallbacks&&String(u.key).startsWith(this._key_prefix))this._changeCallbacks.forEach(function(v){v(u.key,u.oldValue,u.newValue)})};t.prototype.keys=function(){"use strict";var u=[];c("ErrorUtils").guard(function(){if(this._backend){var v=this._backend.keys(),w=this._key_prefix.length;for(var x=0;x<v.length;x++)if(v[x].substr(0,w)==this._key_prefix)u.push(v[x].substr(w))}}.bind(this),"CacheStorage")();return u};t.prototype.set=function(u,v,w){"use strict";if(this._backend){var x;if(typeof v=="string")x=m+v;else if(!w){x={__t:Date.now(),__v:v};x=c("FBJSON").stringify(x)}else x=c("FBJSON").stringify(v);var y=this._backend,z=this._key_prefix+u,A=true;while(A)try{y.set(z,x);A=false}catch(B){var C=y.keys().length;this._evictCacheEntries();A=y.keys().length<C}}};t.prototype._evictCacheEntries=function(){"use strict";var u=[],v=this._backend;v.keys().forEach(function(x){if(x===o)return;var y=v.get(x);if(y===undefined){v.remove(x);return}if(t._hasMagicPrefix(y))return;try{y=c("FBJSON").parse(y,f.id)}catch(z){v.remove(x);return}if(y&&y.__t!==undefined&&y.__v!==undefined)u.push([x,y.__t])});u.sort(function(x,y){return x[1]-y[1]});for(var w=0;w<Math.ceil(u.length/2);w++)v.remove(u[w][0])};t.prototype.get=function(u,v){"use strict";var w;if(this._backend){c("ErrorUtils").applyWithGuard(function(){w=this._backend.get(this._key_prefix+u)},this,null,function(){w=null},"CacheStorage:get");if(w!==null)if(t._hasMagicPrefix(w))w=w.substr(m.length);else try{w=c("FBJSON").parse(w,f.id);if(w&&w.__t!==undefined&&w.__v!==undefined)w=w.__v}catch(x){w=undefined}else w=undefined}if(w===undefined&&v!==undefined){w=v;this.set(u,w)}return w};t.prototype.remove=function(u){"use strict";if(this._backend)c("ErrorUtils").applyWithGuard(this._backend.remove,this._backend,[this._key_prefix+u],null,"CacheStorage:remove")};t.prototype.clear=function(){"use strict";if(this._backend){c("ErrorUtils").applyWithGuard(this._backend.clear,this._backend,null,null,null,"CacheStorage:clear");if(this.useBrowserStorage())this._backend.getStore().setItem(o,n);else this._backend.getStore()[o]=n}};t.getAllStorageTypes=function(){"use strict";return Object.keys(l)};t._hasMagicPrefix=function(u){"use strict";return u.substr(0,m.length)===m};f.exports=t}),null);
__d("VideoPlayerShakaBandwidthEstimator",["CacheStorage","requireWeak","Run","VideoPlayerShakaExperiments"],(function a(b,c,d,e,f,g){var h=void 0;c("requireWeak")("Shaka",function(l){h=l.util.EWMACacheBandwidthEstimator});var i=null,j=false;function k(){"use strict";var l=new(c("CacheStorage"))("localstorage","_video_"),m=l.get("bandwidthEstimate");this.$VideoPlayerShakaBandwidthEstimator1={isMockObject:true,getBandwidth:function n(){return m},getFastMovingBandwidth:function n(){return m}};if(h){this.$VideoPlayerShakaBandwidthEstimator1=new h(c("VideoPlayerShakaExperiments").cacheDelay,c("VideoPlayerShakaExperiments").cacheBandwidth,m);this.$VideoPlayerShakaBandwidthEstimator1.isMockObject=false}c("Run").onUnload(function(){l.set("bandwidthEstimate",this.$VideoPlayerShakaBandwidthEstimator1.getBandwidth())}.bind(this))}k.prototype.getEstimator=function(){"use strict";return this.$VideoPlayerShakaBandwidthEstimator1};k.getInstance=function(){"use strict";if(i===null||i.getEstimator().isMockObject&&h)i=new k();return i};k.getEstimator=function(){"use strict";return k.getInstance().getEstimator()};k.getBandwidth=function(){"use strict";var l=k.getEstimator();return l.getBandwidth()};k.isAutoplayBandwidthRestrained=function(l){"use strict";var m=k.getEstimator(),n=void 0;if(c("VideoPlayerShakaExperiments").blockAutoplayUseFastMovingAverage&&j)n=m.getFastMovingBandwidth();else n=m.getBandwidth();var o=l?c("VideoPlayerShakaExperiments").liveBlockAutoplayBandwidthThreshold:c("VideoPlayerShakaExperiments").blockAutoplayBandwidthThreshold;if(n===null||n>=o)j=false;else j=true;return j};f.exports=k}),null);
__d("AttachmentRelatedShareConstants",[],(function a(b,c,d,e,f,g){f.exports=Object.freeze({ARTICLE_CLICK:"article_click",VIDEO_CLICK:"video_click",FBVIDEO_CLICK:"fbvideo_click",FBVIDEO_VIEW:"fbvideo_view",GAME_CLICK:"game_click",PHOTO_CLICK:"photo_click",EVENT_JOIN:"event_join",PRODUCT_CLICK:"product_click",MAP_CLICK:"map_click",ACTION_BUTTON_CLICK:"action_button_click"})}),null);
__d("MarauderLogger",["Banzai","CacheStorage","MarauderConfig"],(function a(b,c,d,e,f,g){var h="client_event",i="navigation",j=18e4,k="marauder",l="marauder_last_event_time",m="marauder_last_session_id",n={},o=[],p=false,q=null,r=null,s=null,t=0,u,v,w=false,x=null,y=false;function z(){I().set(l,A())}c("Banzai").subscribe(c("Banzai").SHUTDOWN,z);function A(){u=u||I().get(l)||0;return u}function B(){if(!w){v=I().get(m);w=true}var R=Date.now();if(!v||R-j>A()){v=R.toString(16)+"-"+(~~(Math.random()*16777215)).toString(16);I().set(m,v)}return v}function C(){return{user_agent:window.navigator.userAgent,screen_height:window.screen.availHeight,screen_width:window.screen.availWidth,density:window.screen.devicePixelRatio||null,platform:window.navigator.platform||null,locale:window.navigator.language||null}}function D(){return{locale:navigator.language}}function E(R,f,S,T,U,V,W){var X=W||Date.now();u=W?Date.now():X;f=f||q;return{name:R,time:X/1e3,module:f,obj_type:T,obj_id:U,uuid:V,extra:S}}function F(R,S,T){return E("content",null,{flags:S},null,null,R,T)}function G(R){var S=window.__mrdr;if(S)for(var T in S){var U=S[T];if(U[3]!==0){delete S[T];if(T==="1")if(s!==null)T=s;else continue;R.push(F(T,1,U[1]));R.push(F(T,2,U[2]));R.push(F(T,3,U[3]))}}}function H(R){G(R);if(R.length===0)return;if(p)R.push(E("counters",null,n));var S=R[0].name==="time_spent_bit_array"&&c("Banzai").isEnabled("vital_navigations"),T=S?c("Banzai").VITAL:c("Banzai").BASIC,U=c("MarauderConfig").gk_enabled;if(t===0&&U){R.push(E("device_status",null,D()));if(!S)T={delay:5e3}}if(U&&Math.random()<.01)R.push(E("device_info",null,C()));if(s!==null)for(var V=0;V<R.length;V++){var W=R[V];if(W.uuid===null||W.uuid===undefined)W.uuid=s}var X={app_ver:c("MarauderConfig").app_version,data:R,log_type:h,seq:t++,session_id:B()},Y=I().get("device_id");if(Y)X.device_id=Y;n={};p=false;c("Banzai").post(k,X,T)}function I(){if(!y){y=true;x=new(c("CacheStorage"))("localstorage","")}return x}function J(R){if(!n[R])n[R]=0;n[R]++;p=true}function K(R,f,S,T,U,V,W){H([E(R,f,S,T,U,V,W)])}function L(R,S){if(q!==S){o.push(E(i,q,{dest_module:S,source_url:r,destination_url:R}));q=S;r=R}}function M(R,S){if(q!==S){s=null;L(R,S)}}function N(f,R,S){K(R?"show_module":"hide_module",f,S)}function O(f){q=f}function P(){return q}function Q(R){if(s===null){s=R;if(R!==null){H(o);o=[]}}}f.exports={count:J,log:K,navigateTo:M,navigateWithinSession:L,toggleModule:N,setUUID:Q,setNavigationModule:O,getNavigationModule:P}}),null);