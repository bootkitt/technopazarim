<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from urunler where urun_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>Ürün Düzenle</h2>
			<?php 
			
			if($_POST){
				
				$baslik   			=  $_POST["baslik"];
				$fiyat    			=  $_POST["fiyat"];
				$satinal_link    	=  $_POST["satinal_link"];
				$kategori 			=  $_POST["kategori"];
				$aciklama 			=  $_POST["aciklama"];
				$onay     			=  $_POST["onay"];
				$ekleyen		    =  $_SESSION["uye"];
				
				if(!$baslik || !$fiyat || !$aciklama){
					
					echo '<div class="hata">Gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("update urunler set 
					
					      urun_baslik       = ?,
						  urun_fiyat        = ?,
						  urun_satinal_link = ?,
						  urun_kategori     = ?,
						  urun_aciklama     = ?,
						  urun_durum        = ?, 
						  urun_ekleyen		= ? where urun_id =?
					
					");
					
					$update = $guncelle->execute(
					array($baslik,$fiyat,$satinal_link,$kategori,$aciklama,$onay,$ekleyen,$id));
					
					if($update){
						
						echo '<div class="basarili2">Ürün başarıyla güncellendi...</div><meta http-equiv="refresh" content="2;URL=./?do=urunler">';
						
					}else {
						
						echo '<div class="hata">Ürün düzenlenirken bir hata oluştu..</div>'.$guncelle->errorInfo()[2];
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>Ürün İsmi</li>
				<li><input type="text" name="baslik" value="<?php echo $m["urun_baslik"];?>" /></li>
				<li>Ürün Fiyat</li>
				<li><input type="text" name="fiyat" value="<?php echo $m["urun_fiyat"];?>" /></li>
				<li>Ürün Satın Al Link</li>
				<li><input type="text" name="satinal_link" value="<?php echo $m["urun_satinal_link"];?>" /></li>
				<li>Kategori</li>
				<li><select name="kategori" id=""> 
				<?php
				
				$b = $db->prepare("select * from kategoriler order by kategori_id desc");
				$b->execute(array());
				$c = $b->fetchALL(PDO::FETCH_ASSOC);
				
				foreach($c as $z){
					
					echo '<option value="'.$z["kategori_id"].'"';
					
					echo $m["urun_kategori"] == $z["kategori_id"] ? 'selected' : null;
					echo '>'.$z["kategori_adi"].'</option>';
					
				}
				
				?>
				
				</select></li>
				<li>Ürün Açıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"><?php echo $m["urun_aciklama"];?> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1"<?php echo $m["urun_durum"] == 1 ? 'selected' : null;?>>Onaylı</option>
				<option value="0" <?php echo $m["urun_durum"] == 0 ? 'selected' : null;?> >Onaylı Değil</option>
				</select></li>
				<li><button type="submit">Ürün Düzenle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			