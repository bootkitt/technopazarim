<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from konular inner join kategoriler on kategoriler.kategori_id = 

konular.konu_kategori order by konu_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>konular <span style="float:right;"><a href="/admin/?do=konu_ekle">konu ekle</a></span></h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="600">konu baslıkları</th> <th width="300">konu kategorileri</th>
			<th width="200">konu onayları</th> <th width="250">tarih</th>
			<th width="200">işlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["konu_baslik"];?></td> <td><?php echo $m["kategori_adi"];?></td>
					<td> 
					<?php 
					
					if($m["konu_durum"] == 1){
						
						echo '<span style="color:green">onaylı</span>';
						
					}else {
						
						echo '<span style="color:red">onaylı deyil</span>';
						
					}
					
					?>
					
					</td>
					<td><?php echo $m["konu_tarih"];?></td> 
					<td><span style="margin-left:35px;">
					<a href="/admin/?do=konu_duzenle&id=<?php echo $m["konu_id"];?>">duzenle</a></span> <span style="margin-left:10px;"><a onclick="return confirm('konuyu silmek istediğinize eminmisiniz..')" href="/admin/?do=konu_sil&id=<?php echo $m["konu_id"];?>">sil</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="5"><div class="hata">henuz hiç konu 
				eklenmemis...</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>