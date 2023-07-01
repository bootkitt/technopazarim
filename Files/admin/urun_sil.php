<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>Ürün Sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from urunler where urun_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">Ürün başarıyla silindi yönlendiriliyorsunuz..</div><meta http-equiv="refresh" content="2;URL=./?do=urunler">';
			}else {
				
				echo '<div class="hata">Ürün silinirken bir hata oluştu</div>';
				
			}
			?>
			</div>
			</div>