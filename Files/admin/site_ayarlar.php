<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from site_ayarlar inner join kategoriler on kategoriler.kategori_id = 

site_ayarlar.ayarlar order by site_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>Site Ayarları</h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="200">Site Adı</th> 
			<th width="200">Site Sloganı</th>
			<th width="200">Site Açıklama</th> 
			<th width="200">Site Anahtar Kelimeler</th>
			<th width="200">Site Hakkımda</th>
			<th width="200">Şuanki Yıl</th>
			<th width="200">Site Kayan Yazı</th>
			<th width="200">İşlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["site_baslik"];?></td> <td><?php echo $m["site_slogan"];?></td>
					<td> 
					<?php echo $m["site_aciklama"];?>
					</td>
					<td><?php echo $m["site_keyw"];?></td> 
					<td><?php echo $m["site_hakkimda"];?></td>
					<td><?php echo $m["site_yil"];?></td>
					<td><?php echo $m["site_kayanyazi"];?></td>
					<td><span style="margin-left:35px;">
					<a href="./?do=site_ayarlar_duzenle&id=<?php echo $m["site_id"];?>">Düzenle</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="5"><div class="hata">Site ayarları veritabanında bulunamadı!!</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>