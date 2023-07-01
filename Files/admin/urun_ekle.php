<?php !defined("admin") ? die("hacking ?") : null;?>
<div class="admin-icerik-sag"> 
			<h2>Ürün Ekle</h2>
			<?php 
			
			if($_POST){
				
				$baslik   			=  $_POST["baslik"];
				$fiyat    			=  $_POST["fiyat"];
				$satinal_link    	=  $_POST["satinal_link"];
				$kategori 			=  $_POST["kategori"];
				$aciklama 			=  $_POST["aciklama"];
				$onay     			=  $_POST["onay"];
				$urun_ekleyen		=  $_SESSION["uye"];
				
				if(!$baslik || !$fiyat || !$aciklama){
					
					echo '<div class="hata">Gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("insert into urunler set 
					
					      urun_baslik       = ?,
						  urun_fiyat        = ?,
						  urun_satinal_link = ?,
						  urun_kategori     = ?,
						  urun_aciklama     = ?,
						  urun_durum        = ?,
						  urun_ekleyen		= ?
					
					");
					
					$update = $guncelle->execute(
					array($baslik,$fiyat,$satinal_link,$kategori,$aciklama,$onay,$urun_ekleyen));
					
					if($update){
						
						echo '<div class="basarili2">Ürün başarıyla eklendi...</div><meta http-equiv="refresh" content="2;URL=./?do=urunler">';
						
					}else {
						
						echo '<div class="hata">Ürün eklenirken bir hata oluştu..</div>'.$guncelle->errorInfo()[2];
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>Ürün İsmi</li>
				<li><input type="text" name="baslik"  /></li>
				<li>Ürün Fiyat</li>
				<li><input type="text" name="fiyat"  /></li>
				<li>Ürün Satın Al Link</li>
				<li><input type="text" name="satinal_link"  /></li>
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
				<li>Ürün Açıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1">Onaylı</option>
				<option value="0">Onaylı Değil</option>
				</select></li>
				<li><button type="submit">Ürün Ekle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			