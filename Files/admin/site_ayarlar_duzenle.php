<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from site_ayarlar where site_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>Site Ayarlarını Düzenle</h2>
			<?php 
			
			if($_POST){
				
				$baslik   		=  $_POST["baslik"];
				$slogan    		=  $_POST["slogan"];
				$aciklama    	=  $_POST["aciklama"];
				$keyw 			=  $_POST["keyw"];
				$hakkimda 		=  $_POST["hakkimda"];
				$yil 			=  $_POST["yil"];
				$kayanyazi 		=  $_POST["kayanyazi"];
				$onay     		=  $_POST["onay"];
				$ayarlayan		=  $_SESSION["uye"];
				
				if(!$baslik || !$aciklama || !$keyw || !$hakkimda || !$yil){
					
					echo '<div class="hata">Gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("update site_ayarlar set 
					
					      site_baslik        = ?,
						  site_slogan        = ?,
						  site_aciklama      = ?,
						  site_keyw          = ?,
						  site_hakkimda      = ?,
						  site_yil           = ?,
						  site_kayanyazi     = ?,
						  site_durum         = ?,
						  ayarlayan          = ?
					
					");
					
					$update = $guncelle->execute(
					array($baslik,$slogan,$aciklama,$keyw,$hakkimda,$yil,$kayanyazi,$onay,$ayarlayan));
					
					if($update){
						
						echo '<div class="basarili2">Site ayarları başarıyla güncellendi...</div><meta http-equiv="refresh" content="2;URL=./?do=site_ayarlar">';
						
					}else {
						
						echo '<div class="hata">Site ayarları güncellenirken bir hata oluştu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>Site İsmi</li>
				<li><input type="text" name="baslik" value="<?php echo $m["site_baslik"];?>" /></li>
				<li>Site Sloganı</li>
				<li><input type="text" name="slogan" value="<?php echo $m["site_slogan"];?>" /></li>
				<li>Site Açıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"><?php echo $m["site_aciklama"];?> </textarea></li>
				<li>Site Anahtar Kelimeler</li>
				<li><input type="text" name="keyw" value="<?php echo $m["site_keyw"];?>" /></li>
				<li>Site Hakkımda</li>
				<li><input type="text" name="hakkimda" value="<?php echo $m["site_hakkimda"];?>" /></li>
				<li>Şuanki Yıl</li>
				<li><input type="text" name="yil" value="<?php echo $m["site_yil"];?>" /></li>
				<li>Site Kayan Yazı</li>
				<li><textarea name="kayanyazi" id=""><?php echo $m["site_kayanyazi"];?> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1">Onaylı</option>
				<option value="0">Onaylı Değil</option>
				</select></li>
				<li><button type="submit">Ayarlar Güncelle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			