<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from uyeler where uye_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>uye duzenle</h2>
			<?php 
			
			if($_POST){
				
				$adi          =  $_POST["adi"];
				$sifre        =  $_POST["sifre"];
				$eposta       =  $_POST["eposta"];
				$rutbe        =  $_POST["rutbe"];
				$hakkimda     =  $_POST["hakkimda"];
				$onay         =  $_POST["onay"];
				
				if(!$adi ||  !$eposta){
					
					echo '<div class="hata">gerekli alanları doldurmanız gerekiyor..</div>';
					
				}elseif (!filter_var($eposta,FILTER_VALIDATE_EMAIL)){
			  
			  
			  echo '<div class="hata">bu mail adresi gecerli deyil baska bir tane deneyin</div>';
			  
		  } 
				
				else {
					
					if($sifre){
						
						$sifre = md5($sifre);
						
					}else{
						
						$sifre = $m["uye_sifre"];
						
					}
					
					
					
					$guncelle = $db->prepare("update uyeler set 
					
					      uye_adi         = ?,
						  uye_sifre       = ?,
						  uye_eposta      = ?,
						  uye_rutbe       = ?,
						  uye_hakkimda    = ?,
						  uye_onay        = ? where uye_id= ?
					");
					
					$update = $guncelle->execute(
					array($adi,$sifre,$eposta,$rutbe,$hakkimda,$onay,$id));
					
					if($update){
						
						echo '<div class="basarili2">uye basarıyla guncellendi...</div><meta http-equiv="refresh" content="2;URL=./?do=uyeler">';
						
					}else {
						
						echo '<div class="hata">uye eklenirken bir hata olustu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>adi</li>
				<li><input type="text" name="adi" value="<?php echo $m["uye_adi"];?>" /></li>
				<li>sifre</li>
				<li><input type="password" name="sifre" placeholder="yeni sifreyi girin" /></li>
				<li>eposta</li>
				<li><input type="text" name="eposta" value="<?php echo $m["uye_eposta"];?>" /></li>
				<li>rutbe</li>
				<li> 
				<select name="rutbe" id="">
				<option value="0" <?php echo $m["uye_rutbe"] == 0 ? 'selected' : null;?>>uye</option>
				<option value="1" <?php echo $m["uye_rutbe"] == 1 ? 'selected' : null;?>>yonetici</option>
				</select>
				</li>
				<li>uye hakkımda</li>
				<li><textarea name="hakkimda" id="" cols="30" rows="10"><?php echo $m["uye_hakkimda"];?> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1"<?php echo $m["uye_onay"] == 1 ? 'selected' : null;?>>onaylı</option>
				<option value="0" <?php echo $m["uye_onay"] == 0 ? 'selected' : null;?> >onaylı deyil</option>
				</select></li>
				<li><button type="submit">uyeyi duzenle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			