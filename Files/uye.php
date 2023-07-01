<?php 
 !defined("index") ? die("hacking ?") : null;
if($_SESSION){
	
	?>
	<div class="sag3"> 
	<h2>Üye Profili</h2>
	<div style="border:1px solid #ddd;padding:7px;font-size:19px;font-weight:bold;">
	Hoşgeldiniz : <?php echo $_SESSION["uye"];?></div>
	<ul> 
	<?php if($_SESSION["rutbe"] == 1){
		
		echo '<li><a href="./admin/">Admin Paneli</a></li>';
		
	}?>
	<li><a href="?do=profil&uye=<?php echo $_SESSION["uye"];?>">Profil</a></li>
	<li><a href="?do=mesaj">Mesajlarım</a> 
	<?php 
	
	$v = $db->prepare("select * from mesajlar where mesaj_kime=? and mesaj_okunma=?");
	$v->execute(array($_SESSION["id"],0));
	$v->fetchALL(PDO::FETCH_ASSOC);
	$x = $v->rowCount(); 
	
	echo $x;
	
	$v = $db->prepare("select * from mesajlar where mesaj_kime=? and mesaj_okunma=?");
	$v->execute(array($_SESSION["id"],1));
	$v->fetchALL(PDO::FETCH_ASSOC);
	$x = $v->rowCount(); 
	
	echo '<span style="margin-left:5px;">'.$x.'</span>';
	
	?>
	 
	</li>
	<li><a href="?do=konu_ekle">Konu Ekle</a></li>
	<li><a href="?do=cikis">Çıkış</a></li>
	</ul>
	</div>
	<?php
	
}else{
	
	?>
	<div class="sag2"> 
	<ul> 
	<form action="?do=uye" method="post">
	<li>Üye Adı</li>
	<li><input type="text" name="name" /></li>
	<li>Şifreniz</li>
	<li><input type="password" name="sifre" /></li>
	<li><button type="submit">Giriş Yap</button></li>
	</form>
	</ul>
	</div>
	<?php
	
}

?>

