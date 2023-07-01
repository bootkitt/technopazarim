<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>sayfa sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from sabit_sayfalar where sayfa_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">sayfa basarıyla silindi yonlendiriliyorsunuz..</div>';
				header("refresh: 2; url=/admin/?do=sabit_sayfalar");
			}else {
				
				echo '<div class="hata">sayfa silinirken bir hata olustu</div>';
				
			}
			?>
			</div>
			</div>