<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>Duyuru Sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from duyurular where duyuru_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">Duyuru başarıyla silindi yönlendiriliyorsunuz..</div>';
				header("refresh: 2; url=./?do=duyurular");
			}else {
				
				echo '<div class="hata">Duyuru silinirken bir hata oluştu</div>';
				
			}
			?>
			</div>
			</div>