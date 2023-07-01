<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 


   $urunler = $db->prepare("select * from urunler inner join kategoriler on kategoriler.kategori_id =
   
   urunler.urun_kategori ");
   $urunler->execute(array());
   $urunler->fetchALL(PDO::FETCH_ASSOC);
   $urun = $urunler->rowCount();
   
   $ko = $db->prepare("select * from urunler inner join kategoriler on kategoriler.kategori_id =
   
   urunler.urunler_kategori   where urun_durum=?");
   $ko->execute(array(0));
   $ko->fetchALL(PDO::FETCH_ASSOC);
   $urunonay = $ko->rowCount();
   
   
     $uyeler = $db->prepare("select * from uyeler ");
	   $uyeler->execute(array());
	   $uyeler->fetchALL(PDO::FETCH_ASSOC);
	   $uye = $uyeler->rowCount();
   
   $uyeonay = $db->prepare("select * from uyeler   where uye_onay=?");
   $uyeonay->execute(array(0));
   $uyeonay->fetchALL(PDO::FETCH_ASSOC);
   $uyeo = $uyeonay->rowCount();
   
   
       $duyurular = $db->prepare("select * from duyurular ");
	   $duyurular->execute(array());
	   $duyurular->fetchALL(PDO::FETCH_ASSOC);
	   $duyuru = $duyurular->rowCount();
   
   $duyuruonay = $db->prepare("select * from duyurular   where duyuru_onay=?");
   $duyuruonay->execute(array(0));
   $duyuruonay->fetchALL(PDO::FETCH_ASSOC);
   $duyuruonay = $duyuruonay->rowCount();
   
       $kategoriler = $db->prepare("select * from kategoriler ");
	   $kategoriler->execute(array());
	   $kategoriler->fetchALL(PDO::FETCH_ASSOC);
	   $kategori = $kategoriler->rowCount();
	   
	     $ticketler = $db->prepare("select * from ticketler ");
		   $ticketler->execute(array());
		   $ticketler->fetchALL(PDO::FETCH_ASSOC);
		   $ticket = $ticketler->rowCount();

		   
		   
		   ///baslangıc
function dizinboyutu($dizin) {
    $bayt = 0;
    $dzn = opendir($dizin);
    if (!$dzn)
        return -1;
    while (($dosya = readdir($dzn)) !== false) {
        if ($dosya[0] == '.') continue; 
        if (is_dir($dizin . $dosya)){
			$bayt += dizinboyutu($dizin . $dosya . DIRECTORY_SEPARATOR);	
		}else{
			$bayt += filesize($dizin . $dosya);
		}
    }
	closedir($dzn);
	return $bayt;
}


		   //bitis

?>


<div class="admin-icerik-sag"> 
			<h2>Admin Paneli AnaSayfa</h2>
			<div class="anasayfa"> 
			<h3><a href="/admin/?do=urunler">Ürünler</a></h3>
			<p>Toplam Ürün : <?php echo $urun;?> <br />
			Onay Bekleyen Ürün : <?php echo $urunonay;?>
			
			</p>
			</div>
			<div class="anasayfa"> 
			<h3><a href="/admin/?do=uyeler">Üyeler</a></h3>
			<p>
			Toplam Üye : <?php echo $uye;?> <br />
			Onay Bekleyen Üye : <?php echo $uyeo;?>
			
			</p>
			</div>
			<div style="clear:both;"></div>
			<div class="surum">
            <strong>PHP Sürümü :</strong> <span style="color:green"><?php echo phpversion(); 	?></span><br />
			 </span>			
			</div>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			