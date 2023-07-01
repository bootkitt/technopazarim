<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from urunler inner join kategoriler on kategoriler.kategori_id = 

urunler.urun_kategori order by urun_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>Ürünler <span style="float:right;"><a href="./?do=urun_ekle">Ürün Ekle</a></span></h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="250">Ürün İsimleri</th> <th width="100">Ürün Fiyatları</th>
			<th width="240">Ürün Satın Al Link</th>
			<th width="240">Ürün Açıklama</th>
			<th width="200">Ürün Onayları</th> <th width="250">Tarih</th>
			<th width="200">İşlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["urun_baslik"];?></td> <td><?php echo $m["urun_fiyat"];?></td>
					<td><?php echo $m["urun_satinal_link"];?></td> <td><?php echo $m["urun_aciklama"];?></td>
					<td> 
					<?php 
					
					if($m["urun_durum"] == 1){
						
						echo '<span style="color:green">Onaylı</span>';
						
					}else {
						
						echo '<span style="color:red">Onaylı Değil</span>';
						
					}
					
					?>
					
					</td>
					<td><?php echo $m["urun_tarih"];?></td> 
					<td><span style="margin-left:35px;">
					<a href="./?do=urun_duzenle&id=<?php echo $m["urun_id"];?>">Düzenle</a></span> <span style="margin-left:10px;"><a onclick="return confirm('Ürünü silmek istediğinize eminmisiniz..')" href="./?do=urun_sil&id=<?php echo $m["urun_id"];?>">sil</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="10"><div class="hata">Henüz hiç ürün 
				eklenmemiş...</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>