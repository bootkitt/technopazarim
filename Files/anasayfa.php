<?php !defined("index") ? die("hacking ?") : null;?>	
<?php 
  
  $sayfa = intval(@$_GET["sayfa"]);
  
  if(!$sayfa){
	  
	  $sayfa =1;
	  
  }
  
  $v = $db->prepare("select * from urunler inner join kategoriler on 
 
 kategoriler.kategori_id = urunler.urun_kategori inner join uyeler on uyeler.uye_adi= urunler.urun_ekleyen where urun_durum=?");
  $v->execute(array(1));
  $v->fetchALL(PDO::FETCH_ASSOC);
  $toplam = $v->rowCount();
  $limit = 20;
  $goster = $sayfa*$limit-$limit; 
  $sayfa_sayisi =  ceil($toplam/$limit);
  $forlimit = 3;
  
  
  
 $konu = $db->prepare("select * from urunler inner join kategoriler on 
 
 kategoriler.kategori_id = urunler.urun_kategori inner join uyeler on uyeler.uye_adi= urunler.urun_ekleyen where urun_durum=? order by urun_id desc limit $goster,$limit");
 $konu->execute(array(1));
$x =  $konu->fetchALL(PDO::FETCH_ASSOC);

foreach($x as $m){
	?>
	<!-- Single Price Plan Start -->
         <div class="col-md-4 col-xs-12">
            <div class="item">
               <div class="item-top">
                  <h4><?php echo $m["urun_baslik"];?></h4>
                  <h2><?php echo $m["urun_fiyat"];?></h2>
                  <span>Türk Lirası</span>
               </div>
               <div class="item-bottom">
                  <ul>
                     <li><?php echo $m["urun_aciklama"];?></li>
                  </ul>
                  <a href="<?php echo $m["urun_satinal_link"];?>" class="btn choose">Sipariş</a>
               </div>
            </div>
         </div>
	<?php
	
}
  
?>	
	
	
	  
	<div style="clear:both;"></div>
	</div>
	