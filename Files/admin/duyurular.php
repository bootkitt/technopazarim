<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from duyurular inner join kategoriler on kategoriler.kategori_id = 

duyurular.duyuru_kategori order by duyuru_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>Duyurular <span style="float:right;"><a href="./?do=duyuru_ekle">Duyuru Ekle</a></span></h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="600">Duyuru İsimleri</th> <th width="300">Duyuru Kategorileri</th>
			<th width="200">Duyuru Onayları</th> <th width="250">Tarih</th>
			<th width="200">İşlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["duyuru_baslik"];?></td> <td><?php echo $m["kategori_adi"];?></td>
					<td> 
					<?php 
					
					if($m["duyuru_durum"] == 1){
						
						echo '<span style="color:green">Onaylı</span>';
						
					}else {
						
						echo '<span style="color:red">Onaylı Değil</span>';
						
					}
					
					?>
					
					</td>
					<td><?php echo $m["duyuru_tarih"];?></td> 
					<td><span style="margin-left:35px;">
					<a href="./?do=duyuru_duzenle&id=<?php echo $m["duyuru_id"];?>">Düzenle</a></span> <span style="margin-left:10px;"><a onclick="return confirm('Duyuruyu silmek istediğinize eminmisiniz..')" href="./?do=duyuru_sil&id=<?php echo $m["duyuru_id"];?>">Sil</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="5"><div class="hata">Henüz hiç ürün 
				eklenmemisş...</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>