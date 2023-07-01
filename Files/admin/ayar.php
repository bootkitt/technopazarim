<?php 



try {
	
	$host   = "ni1033478-1.web12.nitrado.hosting";//Veritabanı host
	$dbname = "ni1033478_1sql1"; //Veritabanı adı
	$kadi   = "ni1033478_1sql1";//Veritabanı kullanıcı adı
	$sifre  = "ff79be65";//Veritabanı kullanıcı şifresi
	
	
	$db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8","$kadi","$sifre");
	
}catch (PDOException $mesaj){
	
	
	echo $mesaj->getmessage();
	
}

?>