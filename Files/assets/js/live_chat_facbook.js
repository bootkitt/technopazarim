  function check_fist_vist_f() {
    //console.log( "check first: " + f_read_cki('f_chat_open') );
      var _ = f_read_cki("check_fist_vist_f");
      (0 == _ || "" == _) && (fb_eshow("f-chat-conent"), f_create_cki("check_fist_vist_f", "1", 1), f_create_cki("f_chat_open", "1", 1))
      //console.log( "check first: " + f_read_cki('f_chat_open') );
  }

  function chat_f_close() {
      fb_ehide('b-c-facebook'), f_create_cki('chat_f_close', 1, 1), $('body').find('.zopim').remove(), fb_eshow('chat_f_b_smal'), on_playsound('click')
  }

  function chat_f_show() {
      f_create_cki('chat_f_close', '0', 1), fb_eshow('b-c-facebook'), fb_eshow('f-chat-conent'), fb_ehide('chat_f_b_smal')
  }

  function f_bt_start_chat() {
      f_create_cki('f_bt_start_chat', '1', 10), fb_ehide('fb_chat_start'), fb_ehide('fb_alert_num'), on_playsound('click')
  }

  function f_c_start_chat() {
      var t = f_read_cki('f_bt_start_chat');
      0 == t || '' == t ? (fb_eshow('fb_chat_start'), fb_eshow('fb_alert_num'), f_chat_step()) : (fb_ehide('fb_chat_start'), fb_ehide('fb_alert_num'))
  }

  function b_f_chat() {
    //console.log( "check first d: " + f_read_cki('f_chat_open') );
      var t = f_read_cki('f_chat_open');
      0 == t || '' == t ? (fb_eshow('f-chat-conent'), f_create_cki('f_chat_open', '1', 1)) : (fb_ehide('f-chat-conent'), f_create_cki('f_chat_open', '0', 1)), on_playsound('click')
    //console.log( "check first d: " + f_read_cki('f_chat_open') );
  }

  function f_ck_chat() {
      check_fist_vist_f();
      f_c_start_chat();
      var t = f_read_cki('chat_f_close');
      if ('' == t || 0 == t || '0' == t) {
          fb_eshow('b-c-facebook'), fb_ehide('chat_f_b_smal');
          var e = f_read_cki('f_chat_open');
                //console.log(e);
          (1 == e || '1' == e) && fb_eshow('f-chat-conent')
      } else fb_eshow('chat_f_b_smal'), fb_ehide('b-c-facebook')
  }

  function f_chat_step() {
      on_playsound('door_bell')
      fb_eshow('f_enter_1');
      fb_eshow('f_enter_3');
  }

  function fb_eshow(t) {
      document.getElementById(t).style.display = 'block';
  }

  function fb_ehide(t) {
      document.getElementById(t).style.display = 'none';
  }

  function f_create_cki(t, e, n) {
      if (n) {
          var o = new Date;
          o.setTime(o.getTime() + 24 * n * 60 * 60 * 1e3);
          var c = '; expires=' + o.toGMTString()
      } else var c = '';
      document.cookie = t + '=' + e + c + '; path=/';
  }

  function f_read_cki(t) {
      for (var e = t + '=', n = document.cookie.split(';'), o = 0; o < n.length; o++) {
          for (var c = n[o];
              ' ' == c.charAt(0);) c = c.substring(1, c.length);
          if (0 == c.indexOf(e)) return c.substring(e.length, c.length)
      }
      return ''
  }

  function on_playsound(t) {
     // 1 == web_sound && $.ionSound.play(t)
  }

  function ionSound() {
      1 == web_sound && $.ionSound({
          sounds: ['click', 'door_bell'],
          path: fb_path.live_chat_path + 'sounds/',
          multiPlay: !0,
          volume: '1.0'
      })
  }
  var web_sound = !0;
 
  var $ = jQuery.noConflict();
  ! function(t) {
      if (!t.ionSound) {
          var e, n, o, c, _ = {}, f = {}, a = !1,
              i = function(e) {
                  var c, a; - 1 !== e.indexOf(':') ? (c = e.split(':')[0], a = e.split(':')[1]) : c = e, f[c] = new Audio, n = f[c].canPlayType('audio/mp3'), o = 'probably' === n || 'maybe' === n ? _.path + c + '.mp3' : _.path + c + '.ogg', t(f[c]).prop('src', o), f[c].load(), f[c].preload = 'auto', f[c].volume = a || _.volume
              }, u = function(t) {
                  var e, n, o, c;
                  if (-1 !== t.indexOf(':') ? (n = t.split(':')[0], o = t.split(':')[1]) : n = t, e = f[n], 'object' == typeof e && null !== e)
                      if (o && (e.volume = o), _.multiPlay || a) {
                          if (_.multiPlay)
                              if (e.ended) e.play();
                              else {
                                  try {
                                      e.currentTime = 0
                                  } catch (i) {}
                                  e.play()
                              }
                      } else e.play(), a = !0, c = setInterval(function() {
                          e.ended && (clearInterval(c), a = !1)
                      }, 250)
              }, l = function(t) {
                  var e = f[t];
                  if ('object' == typeof e && null !== e) {
                      e.pause();
                      try {
                          e.currentTime = 0
                      } catch (n) {}
                  }
              }, r = function(t) {
                  var e = f[t];
                  if ('object' == typeof e && null !== e) {
                      try {
                          f[t].src = ''
                      } catch (n) {}
                      f[t] = null
                  }
              };
          t.ionSound = function(n) {
              if (_ = t.extend({
                  sounds: ['water_droplet'],
                  path: '/sounds/',
                  multiPlay: !0,
                  volume: '0.5'
              }, n), e = _.sounds.length, 'function' == typeof Audio || 'object' == typeof Audio)
                  for (c = 0; e > c; c += 1) i(_.sounds[c]);
              t.ionSound.play = function(t) {
                  u(t)
              }, t.ionSound.stop = function(t) {
                  l(t)
              }, t.ionSound.kill = function(t) {
                  r(t)
              }
          }, t.ionSound.destroy = function() {
              for (c = 0; e > c; c += 1) f[_.sounds[c]] = null;
              e = 0, t.ionSound.play = function() {}, t.ionSound.stop = function() {}, t.ionSound.kill = function() {}
          }
      }
  }(jQuery);

