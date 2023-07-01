<?php !defined("admin") ? die("hacking ?") : null;?>
<div class="admin-icerik-sag"> 
			<h2>Duyuru Ekle</h2>
			<?php 
			
			if($_POST){
				
				$baslik   =  $_POST["baslik"];
				$resim    =  $_POST["resim"];
				$kategori =  $_POST["kategori"];
				$aciklama =  $_POST["aciklama"];
				$onay     =  $_POST["onay"];
				
				
				if(!$baslik || !$resim || !$aciklama){
					
					echo '<div class="hata">Gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("insert into duyurular set 
					
					      duyuru_baslik   = ?,
						  duyuru_resim    = ?,
						  duyuru_kategori = ?,
						  duyuru_aciklama = ?,
						  duyuru_durum    = ?,
						  duyuru_ekleyen  = ? 
					
					");
					
					$update = $guncelle->execute(
					array($baslik,$resim,$kategori,$aciklama,$onay,$_SESSION["uye"]));
					
					if($update){
						
						echo '<div class="basarili2">Duyuru başarıyla eklendi...</div>';
						
						header("refresh: 2; url=./?do=duyurular");
						
					}else {
						
						echo '<div class="hata">Duyuru eklenirken bir hata olustu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>Duyuru İsmi</li>
				<li><input type="text" name="baslik"  /></li>
				<li>Resim Linki</li>
				<li><input type="text" name="resim"  /></li>
				<li>Kategori</li>
				<li><select name="kategori" id=""> 
				<?php
				
				$b = $db->prepare("select * from kategoriler order by kategori_id desc");
				$b->execute(array());
				$c = $b->fetchALL(PDO::FETCH_ASSOC);
				
				foreach($c as $z){
					
					echo '<option value="'.$z["kategori_id"].'">'.$z["kategori_adi"].'</option>';
					
				}
				
				?>
				
				</select></li>
				<li>Açıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1">Onaylı</option>
				<option value="0">Onaylı Değil</option>
				</select></li>
				<li><button type="submit">Duyuru Ekle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			