<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$id = $_GET["id"];

$v = $db->prepare("select * from yorumlar where yorum_id=?");
$v->execute(array($id));

$m = $v->fetch(PDO::FETCH_ASSOC);

?>

<div class="admin-icerik-sag"> 
			<h2>yorum duzenle</h2>
			<?php 
			
			if($_POST){
				
				$mesaj   =  $_POST["mesaj"];
				$onay    =  $_POST["onay"];
				
				
				if(!$mesaj){
					
					echo '<div class="hata">gerekli alanları doldurmanız gerekiyor..</div>';
					
				}else {
					
					$guncelle = $db->prepare("update yorumlar set 
					
					      yorum_mesaj   = ?,
						  yorum_onay    = ?  where yorum_id =?
					
					");
					
					$update = $guncelle->execute(
					array($mesaj,$onay,$id));
					
					if($update){
						
						echo '<div class="basarili2">yorum basarıyla guncellendi...</div>';
						
						header("refresh: 2; url=/admin/?do=yorumlar");
						
					}else {
						
						echo '<div class="hata">yorum guncellenirken bir hata olustu..</div>';
						
					}
				}
				
				
			}else {
				
				?>
				<div class="konular"> 
				<form action="" method="post">
				<ul> 
				<li>yorum mesajı</li>
				<li><textarea name="mesaj" id="" cols="30" rows="10"><?php echo $m["yorum_mesaj"];?> </textarea></li>
				<li><select name="onay" id=""> 
				<option value="1"<?php echo $m["yorum_onay"] == 1 ? 'selected' : null;?>>onaylı</option>
				<option value="0" <?php echo $m["yorum_onay"] == 0 ? 'selected' : null;?> >onaylı deyil</option>
				</select></li>
				<li><button type="submit">yorumu duzenle</button></li>
				</ul>
				</form>
				</div>
				<?php
				
			}
			
			?>
			</div>
			
			
			
			
			
			
			
			
			
			
			
			
			
			
			