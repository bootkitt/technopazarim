<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from kategoriler where kategori_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>Kategori Düzenle</h2>
			<?php 
			
			if($_POST){
				
				$adi   =  trim(strip_tags($_POST["adi"]));
				$aciklama    =  trim(strip_tags($_POST["aciklama"]));
				
				
				if(!$adi || !$aciklama){
					
					echo '<div class="hata">Gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("update kategoriler set 
					
					      kategori_adi         = ?,
						  kategori_aciklama    = ?  where kategori_id =?
					
					");
					
					$update = $guncelle->execute(
					array($adi,$aciklama,$id));
					
					if($update){
						
						echo '<div class="basarili2">Kategori baŞarıyla güncellendi...</div><meta http-equiv="refresh" content="2;URL=./?do=kategoriler">';
						
					}else {
						
						echo '<div class="hata">Kategori eklenirken bir hata oluştu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>Kategori Adı</li>
				<li><input type="text" name="adi" value="<?php echo $m["kategori_adi"];?>" /></li>
		
				<li>Kategori Açıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"><?php echo $m["kategori_aciklama"];?> </textarea></li>
				<li><button type="submit">Kategori Düzenle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			