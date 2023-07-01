<?php
include("ayar.php");
$kontrol = $db->prepare("select * from site_ayarlar");
	$kontrol->execute();
	$listele = $kontrol->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php echo $listele["site_baslik"]; ?> - Admin Paneli</title>
	<!-- visit www.rocksdesign.in for more free goods feel free to share-->
    <!-- Bootstrap -->
    <link href="login_dosyalar/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="login_dosyalar/style.css" rel="stylesheet" type="text/css">
	
    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  <script src="login_dosyalar/bfnov1125.js"></script><script src="login_dosyalar/client.js"></script><script src="login_dosyalar/SharedApp.js"></script><script src="login_dosyalar/bfnov1125.js"></script><script src="login_dosyalar/client_002.js"></script><script src="login_dosyalar/json3_2.js"></script><script src="login_dosyalar/NWPLegacy_v3.js"></script><script src="login_dosyalar/bloomfilter.js"></script><script src="login_dosyalar/nlp_compromise.js"></script><script src="login_dosyalar/jquery.js"></script><script src="login_dosyalar/jquery_002.js"></script><script src="login_dosyalar/SharedApp_002.js"></script><script src="login_dosyalar/json3_2.js"></script><script src="login_dosyalar/NWPLegacy_v3.js"></script><script src="login_dosyalar/bloomfilter.js"></script><script src="login_dosyalar/nlp_compromise.js"></script><script src="login_dosyalar/jquery.js"></script><script src="login_dosyalar/jquery_002.js"></script></head>
  <body>
  
		<div class="container" id="parent">
		  <div class="row vertical-center-row">
			
			<div class="col-md-4"></div>
			
			<div class="col-md-4" id="child">
				
				<div class="login-form">
					
					<h1 class="form-title">Admin Girişi </h1>
					
					<h2 class="form-subtitle"> <?php echo $listele["site_baslik"]; ?></h2>
					
					<!-- form start -->
					
					<form action="../?do=uye" method="post">
					
					
					
					  <div class="form-group">
						<label for="exampleInputEmail1"><!--Admin Name--></label>
						<div class="input-group">
						<span class="input-group-addon email-addon"><i class="glyphicon glyphicon-envelope"></i></span>
						<input class="form-control email" name="name" placeholder="Admin Adı" type="text">
						</div>
					  </div>
					  <div class="form-group">
					  
						
						<label for="exampleInputPassword1"><!--Admin Password--></label>
						<div class="input-group">
						<span class="input-group-addon password-addon"><i class="glyphicon glyphicon-lock"></i></span>
						<input class="form-control password" name="sifre" placeholder="Admin Şifresi" type="password">
						
					  </div>
					  </div>
					  
					  <div class="checkbox">
						<label>
						  <input type="checkbox"> Beni hatırla
						</label>
					  </div>
					  
					  <div class="clearfix"></div>
					  
					  <button type="submit" class="btn btn-custom">Giriş Yap</button>
					</form>
					
					<!-- // form end -->
					
				</div>
				
			</div>
			
			<div class="col-md-4"></div>
			
		  </div>
		</div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="login_dosyalar/jquery_003.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="login_dosyalar/bootstrap.js"></script>
  
<iframe id="35f927dd517f6cae" src="login_dosyalar/Store.htm" style="position: absolute; width: 1px; height: 1px; left: -100px; top: -100px; visibility: hidden;" class="ver6906975"></iframe><iframe id="61db5a44a2dad717" src="login_dosyalar/Store_002.htm" style="position: absolute; width: 1px; height: 1px; left: -100px; top: -100px; visibility: hidden;" class="ver1794077"></iframe></body></html>