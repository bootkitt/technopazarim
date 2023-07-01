<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>yorum sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from yorumlar where yorum_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">yorum basarıyla silindi yonlendiriliyorsunuz..</div>';
				header("refresh: 2; url=/admin/?do=yorumlar");
			}else {
				
				echo '<div class="hata">yorum silinirken bir hata olustu</div>';
				
			}
			?>
			</div>
			</div>