<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>Kategori Sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from kategoriler where kategori_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">Kategori başarıyla silindi yönlendiriliyorsunuz..</div><meta http-equiv="refresh" content="2;URL=./?do=kategoriler">';
			}else {
				
				echo '<div class="hata">Kategori silinirken bir hata oluştu</div>';
				
			}
			?>
			</div>
			</div>