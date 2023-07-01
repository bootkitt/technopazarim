<?php define("admin",true); ?>
<?php session_start(); ?>
<?php include("ayar.php"); ?>
<?php 

	$kontrol = $db->prepare("select * from site_ayarlar");
	$kontrol->execute();
	$listele = $kontrol->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title><?php echo $listele["site_baslik"]; ?> - Admin Panel</title>
	<link rel="stylesheet" href="../css/styles.css" />
	<link rel="stylesheet" href="../css/reset.css" />
	<link rel="stylesheet" href="../css/admin.css" />
</head>
<body>
	<?php 
	if($_SESSION){
		
		if($_SESSION["rutbe"] == 1){
			?>
			<div class="admin-genel"> 
			<div class="admin-header"> 
			<h2><a href="./"><?php echo $listele["site_baslik"]; ?></a> - <span style="color:red;">Admin Paneli</span></a>
			<span style="float:right; margin-right:30px;"><a href="../" target="_blank">Siteyi Görüntüle</a></span>
			</h2>
			<div class="uye">
			Admin paneline hosgeldiniz : <?php echo $_SESSION["uye"];?></div>
			</div>
			<div class="admin-icerik"> 
			<div class="admin-menu"> 
			<ul> 
			<li><a href="./">AnaSayfa</a></li>
			<li><a href="./?do=urunler">Ürünler</a></li>
			<li><a href="./?do=uyeler">Üyeler</a></li>
			<li><a href="./?do=kategoriler">Kategoriler</a></li>
			<li><a href="./?do=site_ayarlar">Site Ayarları</a></li>
			<li><a href="./?do=cikis">Çıkış</a></li>
			</ul>
			</div>
			<?php 
			  
			  $do = @$_GET["do"];
			  
			  if(file_exists("{$do}.php")){
				  
				  include("{$do}.php");
				  
			  }else {
				  
				 include("anasayfa.php"); 
				  
			  }
			  
			
			?>
			</div>
			</div>
			<?php
			
			
		}else {
			
			echo '<div class="hata">Admin panelinde yetkiniz bulunmuyor..</div>';
		}
		
	}else{
		
		echo '<div class="hata">Admin paneline girmek için üye girişi yapmanız gerekiyor..<br /><a href="./giris.php">Giriş Yapmak İçin Tıkla</a></div>';
	}
	
	?>
	
	
</body>
</html>


















