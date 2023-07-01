<?php !defined("index") ? die("hacking ?") : null;?>	
<?php 
  
  $sayfa = intval(@$_GET["sayfa"]);
  
  if(!$sayfa){
	  
	  $sayfa =1;
	  
  }
  
  $v = $db->prepare("select * from site_ayarlar inner join kategoriler on 
 
 kategoriler.kategori_id = site_ayarlar.site_kayanyazi");
  $v->execute(array(1));
  $v->fetchALL(PDO::FETCH_ASSOC);
  $toplam = $v->rowCount();
  $limit = 5;
  $goster = $sayfa*$limit-$limit; 
  $sayfa_sayisi =  ceil($toplam/$limit);
  $forlimit = 3;
  
  
  
 $konu = $db->prepare("select * from urunler inner join kategoriler on 
 
 kategoriler.kategori_id = urunler.urun_kategori inner join uyeler on uyeler.uye_adi= urunler.urun_ekleyen where urun_durum=? order by urun_id desc limit $goster,$limit");
 $konu->execute(array(1));
$x =  $konu->fetchALL(PDO::FETCH_ASSOC);

foreach($x as $m){
	?>
				<h3 class="cd-headline clip is-full-width"><span class="cd-words-wrapper"> <b>%100 Güvenli</b> </span> </h3>
	<?php
	
}
  
?>	
	
	
	  
	<div style="clear:both;"></div>
	</div>
	