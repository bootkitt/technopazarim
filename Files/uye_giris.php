<?php 
 !defined("index") ? die("hacking ?") : null;
 if($_POST){
	 
	 $name = trim($_POST["name"]);
	 $sifre = trim(md5($_POST["sifre"]));
	 
    if(!$name || !$sifre){
		
		echo '<div class="hata">Kullancı adı ve şifre boş bırakılamaz</div>';
		
		
	}else {
		
		$uye = $db->prepare("select * from uyeler where uye_adi=? and uye_sifre=? and uye_onay=?");
		$uye->execute(array($name,$sifre,1));
		$z = $uye->fetch(PDO::FETCH_ASSOC);
		$x = $uye->rowCount();
		
		if($x){
			
			$_SESSION["uye"] = $z["uye_adi"];
			$_SESSION["eposta"] = $z["uye_eposta"];
			$_SESSION["rutbe"] = $z["uye_rutbe"];
			$_SESSION["id"] = $z["uye_id"];
			
			header("location:admin");
			
		}elseif($z["uye_onay"] == 0){
			
			echo '<div class="hata">Üyeliğiniz onaylanmadı yönetici onayını bekleyin..</div>';
			
		} else {
			
			echo '<div class="hata">Üye adı ya da şifreniz yanlış</div>';
			
		}
	}	
	
 }



?>