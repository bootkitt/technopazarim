<?php define("index",true);?>
<?php include("ayar.php");?>
<?php session_start();
ob_start();
	$kontrol = $db->prepare("select * from site_ayarlar");
	$kontrol->execute();
	$listele = $kontrol->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="tr">
   
	  <meta http-equiv="content-type" content="text/html;charset=UTF-8" />
<head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- Title of Website -->
      <title><?php echo $listele["site_baslik"]; ?> <?php echo $listele["site_slogan"]; ?></title>
      <meta name="description" content="<?php echo $listele["site_aciklama"]; ?>"/>
	    <meta name="keywords" content="<?php echo $listele["site_keyw"]; ?>">
      <meta property="og:image" content="assets/images/social.html">
      <meta property="og:image:type" content="image/png">
      <meta property="og:image:width" content="250">
      <meta property="og:image:height" content="250">
      <meta property="og:description" content="<?php echo $listele["site_aciklama"]; ?>">
      <!-- Favicon -->
      <script src="cdn-cgi/apps/head/AdloEShmAhgmAqmSZs9jrqogRl0.js"></script><link rel="shortcut icon" type="image/x-icon" href="assets/images/baslogo.png">
      <!-- Bootstrap CSS -->
      <link rel="stylesheet" href="assets/css/plugins/bootstrap.min.css">
      <!-- Custom CSS -->
	  <?php
	  
		$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] ); 
		if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap |mot-|webos\/|samsung|sonyericsson|^sie-|nintendo/", $user_agent ) ) { 
			echo '<link rel="stylesheet" href="assets/css/mobile.css">'; 
		} else { 
			echo '<link rel="stylesheet" href="assets/css/style.css">'; 
		}
	  ?>
	  <link rel="stylesheet" href="assets/css/live_chat_facbook.css">
      <!-- Color Switcher CSS -->
      <link href="assets/switcher/switcher.css" rel="stylesheet">
      <link href="assets/switcher/style1.css" rel="stylesheet" id="colors">
      <script src="assets/js/vendor/modernizr-2.8.3.min.js"></script>
      <script src="../cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.min.js"></script>
      <link rel="stylesheet" href="../cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.6.4/sweetalert2.min.css" />
      <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
      <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
      <![endif]-->
	  
<script type="text/javascript" class="solTaraf" style=".solTaraf{float:left;}">window.$crisp=[];window.CRISP_WEBSITE_ID="6f1f86c3-c957-4c29-9505-30f27ab632f1";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
	  
   </head>
   <!-- Body Start -->
 <body>
    <!-- Header Start  -->
    <header>
       <div class="container">
          <a href="<?php $_SERVER['HTTP_HOST']; ?>/./"><h2 class="logo"><?php echo $listele["site_baslik"]; ?></h2></a>
          <div id="nav-icon">
             <span></span>
             <span></span>
             <span></span>
          </div>
                    <ul class="site-menu">
             <form action="">
			 
				<li><a href="<?php $_SERVER['HTTP_HOST']; ?>/./#home">Anasayfa</a></li>
				<li><a href="<?php $_SERVER['HTTP_HOST']; ?>/./#pricing">Satın Al</a></li>
				<li><a href="<?php $_SERVER['HTTP_HOST']; ?>/./#about">Hakkımızda</a></li>
				<li><a href="<?php $_SERVER['HTTP_HOST']; ?>/./iletisim">Bizimle İletişime Geç</a></li>
			 
			 </form>
			 <!--<a href="promosyon.html" class="btn choose popup">Promosyon</a>-->
          </ul>
                 </div>
    </header>
    <!-- Header End -->
        <!-- Home Section Start -->
    <div class="section hero parallax" id="home" style="background-image:url(assets/images/banner.jpg);" data-stellar-background-ratio="0.5">
       <div class="content col-xs-12">
          <div class="dta text-center animation fadeInUp delay2">
             <div class="container">
                <h2><?php echo $listele["site_baslik"]; ?></h2>
                <h3 class="cd-headline clip is-full-width"><span class="cd-words-wrapper"> <?php echo $listele["site_kayanyazi"]; ?> </span> </h3>
             </div>
          </div>
       </div>
       <div class="doWn bounce"><span class="icon-down-round"></span></div>
    </div>
    <!-- Home Section End -->
<!-- Pricing Section Start -->
<div class="section pricing animation fadeInUp" id="pricing">
   <div class="container">
      <div class="row">
		 <?php 
   
   $do = @$_GET["do"];
   
    switch ($do){
		
		
		case "uye":
		include("uye_giris.php");
		break;
		
		case "cikis":
		
		session_destroy();
		
		header("refresh: 2; url=index.php");
		
		echo '<div class="basarili2">Basarıyla çıkış yaptınız yönlendiriliyorsunuz</div>';
		
		break;
		
		case "devam":
		include ("devam.php");
		break;
		
		case "iletisim":
		include ("iletisim.php");
		break;
		
		default :
		
		include ("anasayfa.php");
		break;
		
		
	}
   
   
   
   
   ?>
      </div>
   </div>
</div>
<!-- Pricing Section End -->
<!-- About Section Start -->
<div class="about section rmvbtm-padding" id="about">
   <div class="container">
      <!-- Profile Start -->
      <h2 class="head">Hakkımızda</h2>
      <h3 class="sub-head">Bizi Tanımak İsterseniz..</h3>
      <div class="row">
         <div class="col-md-5 animation fadeInLeft">
            <img class="img-responsive" src="assets/images/skin.png" alt="">
         </div>
         <div class="col-md-7 about-right animation fadeInRight">
            <p><?php echo $listele["site_hakkimda"]; ?>
            </p>
            <div class="row">
               <!-- Experience Start -->
               <!--<div class="col-sm-12">
                  <h3>Zamanda Biz</h3>
                  <ul class="work-exp">
                     <li>
                        <h4>45000+ Mutlu Müşteri</h4>
                        <span>2017</span>
                     </li>
                     <li>
                        <h4>İlk Satış</h4>
                        <span>2017</span>
                     </li>
                     <li>
                        <h4>TechnoPazarım Kuruldu</h4>
                        <span>2017</span>
                     </li>
                  </ul>
               </div>-->
               <!-- Experience End -->
            </div>
         </div>
      </div>
   </div>
</div>
<!-- About Section End -->
<!-- Services Section Start -->
<div class="section services animation fadeInUp" id="what-we-do">
   <div class="container">
      <div class="row">
         <div class="col-md-12">
            <h2 class="head">Hizmetlerimiz</h2>
            <h3 class="sub-head">Hangi Hizmetleri Sunuyoruz ?</h3>
            <!-- Single Service Start -->
            <div class="col-md-4 col-xs-6 col-mob">
               <div class="item">
                  <i class="icon-smartphone-call"></i>
                  <h3>Mobil Ödeme</h3>
                  <p>Kredi Kartınız Yoksa Mobil Ödememiz Var Mobil Ödemelerde Extra Bir Ücret Olmaksızın Aynı Fiyata Hesap Satın Alabilirsiniz.</p>
               </div>
            </div>
            <!-- Single Service End -->
            <!-- Single Service Start -->
            <div class="col-md-4 col-xs-6 col-mob">
               <div class="item">
                  <i class="icon-price-tag"></i>
                  <h3>İninal - Kredi Kartı Ödemesi</h3>
                  <p>Kişisel bilgilerinizin güvenliği bizim için en önemli konulardan biri. 3D Secure Kredi Kartı Ödemesi ile Güvenli Bir Şekilde Ödeme Yapabilirsiniz</p>
               </div>
            </div>
            <!-- Single Service End -->
            <!-- Single Service Start -->
            <div class="col-md-4 col-xs-6 col-mob">
               <div class="item">
                  <i class="icon-layers"></i>
                  <h3>Anında Teslimat</h3>
                  <p>Hesabınız ödemeyi yaptıktan ve onayladıktan sonra ekranınızda karşınıza çıkıcaktır. Hemen hesap bilgilerinizi girerek satın aldığınız oyunu oynayabilirsiniz.</p>
               </div>
            </div>
            <!-- Single Service End -->
            <!-- Single Service Start -->
            <div class="col-md-4 col-xs-6 col-mob">
               <div class="item">
                  <i class="icon-line-chart"></i>
                  <h3>Tek Tıkla Değişim</h3>
                  <p>Eğer Hesabınızda Bir Problem Çıktıysa Girmiyor veya Banlıysa Tek Tıkla Değişim Gerçekleştirebilirsiniz Zaman Kaybı Yaşamazsınız. </p>
               </div>
            </div>
            <!-- Single Service End -->
         </div>
      </div>
   </div>
</div>
<!-- Services Section Start -->

<!-- Footer Section Start -->
<footer class="footer">
   <div class="back-top"><i class="icon-up-round"></i></div>
   <div class="container">
	  <div class="gri">
	  
		<p>© <a href="<?php $_SERVER['HTTP_HOST']; ?>/./"><?php echo $listele["site_baslik"]; ?></a> <?php echo $listele["site_yil"]; ?> Tüm Hakları Saklıdır.</p>
	  
	  </div>
   </div>
</footer>
<!-- Footer Section End -->

<!-- Scripts -->
<!-- jQuery -->
<script src="assets/js/vendor/jquery-1.12.4.min.js"></script>
<script src="assets/js/vendor/jquery.easing-1.3.pack.js"></script>
<!-- Pace -->
<script src="assets/js/plugins/pace.min.js"></script>
<!-- Bootsrap core -->
<script src="assets/js/plugins/bootstrap.min.js"></script>

<!-- Plugins -->
<script src="assets/js/plugins/title-animated.js"></script>
<script src="assets/js/plugins/jquery.smooth-scroll.min.js"></script>
<script src="assets/js/plugins/jquery.waypoints.min.js"></script>
<script src="assets/js/plugins/device-detect.js"></script>
<script src="assets/js/plugins/owl.carousel.js"></script>
<script src="assets/js/plugins/jquery.stellar.min.js"></script>
<script src="assets/js/plugins/jquery.magnific-popup.min.js"></script>
<script src="assets/js/live_chat_facbook.js"></script>

<!-- Custom Script -->
<script src="assets/js/function.js"></script>
<script type="text/javascript">
   jQuery(document).ready(function($) {
        $(window).scroll(function() {
            var e = $(window).width();
            //680 >= e ? f_create_cki('f_chat_open', '0', 1) : f_create_cki('f_chat_open', '1', 1)
        })
    }), setTimeout(function() {
          var visit = f_read_cki("check_fist_vist_f");
          if ( visit == "1" ) {
              f_ck_chat();
          }else{
              f_ck_chat();
              fb_ehide("f-chat-conent");
              f_create_cki('f_chat_open', '0', 1);
              f_bt_start_chat();
         }
    }, 1000);
   
</script>
<a title="Open Chat" id="chat_f_b_smal" onclick="chat_f_show()" class="chat_f_vt">Open Chat</a>
<div id="fb-root"></div>
<script>(function(d, s, id) {
   var js, fjs = d.getElementsByTagName(s)[0];
   if (d.getElementById(id)) return;
   js = d.createElement(s); js.id = id;
   js.src = "../connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5";
   fjs.parentNode.insertBefore(js, fjs);
   }(document, 'script', 'facebook-jssdk'));
  
</script>
</body>

</html>
