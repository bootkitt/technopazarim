<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>uye sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from uyeler where uye_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">Üye başarıyla silindi yönlendiriliyorsunuz..</div><meta http-equiv="refresh" content="2;URL=./?do=uyeler">';
			}else {
				
				echo '<div class="hata">Üye silinirken bir hata oluştu</div>';
				
			}
			?>
			</div>
			</div>