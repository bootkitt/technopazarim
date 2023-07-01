<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from sabit_sayfalar where sayfa_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>sayfa duzenle</h2>
			<?php 
			
			if($_POST){
				
				$adi   =  trim(strip_tags($_POST["adi"]));
				$aciklama    =  trim(strip_tags($_POST["aciklama"]));
				
				
				if(!$adi || !$aciklama){
					
					echo '<div class="hata">gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("update sabit_sayfalar set 
					
					      sayfa_adi         = ?,
						  sayfa_aciklama    = ?  where sayfa_id =?
					
					");
					
					$update = $guncelle->execute(
					array($adi,$aciklama,$id));
					
					if($update){
						
						echo '<div class="basarili2">sayfa  basarıyla guncellendi...</div>';
						
						header("refresh: 2; url=/admin/?do=sabit_sayfalar");
						
					}else {
						
						echo '<div class="hata">sayfa eklenirken bir hata olustu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>sayfa adi</li>
				<li><input type="text" name="adi" value="<?php echo $m["sayfa_adi"];?>" /></li>
		
				<li>sayfa acıklama</li>
				<li><textarea name="aciklama" id="" cols="30" rows="10"><?php echo $m["sayfa_aciklama"];?> </textarea></li>
				<li><button type="submit">sayfa duzenle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			