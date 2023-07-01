<?php !defined("admin") ? die("hacking ?") : null;?>
<?php

$id = $_GET["id"];



?>

<div class="admin-icerik-sag"> 
			<h2>konu sil</h2>
			<div class="konular"> 
			<?php
			$v = $db->prepare("delete from konular where konu_id=?");
			$sil = $v->execute(array($id));
			if($sil){
				
				echo '<div class="basarili2">konu basarıyla silindi yonlendiriliyorsunuz..</div>';
				header("refresh: 2; url=/admin/?do=konular");
			}else {
				
				echo '<div class="hata">konu silinirken bir hata olustu</div>';
				
			}
			?>
			</div>
			</div>