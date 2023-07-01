<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from kategoriler order by kategori_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>Kategoriler <span style="float:right;"><a href="./?do=kategori_ekle">Kategori Ekle</a></span></h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="500">Kategori Adı</th> <th width="700">Kategori Açıklama</th>
			
			<th width="200">İşlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["kategori_adi"];?></td> <td><?php echo $m["kategori_aciklama"];?></td>
					
					
					<td><span style="margin-left:35px;">
					<a href="./?do=kategori_duzenle&id=<?php echo $m["kategori_id"];?>">Düzenle</a></span> <span style="margin-left:10px;"><a onclick="return confirm('Kategoriyi silmek istediğinize eminmisiniz..')" href="./?do=kategori_sil&id=<?php echo $m["kategori_id"];?>">sil</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="5"><div class="hata">Henüz hiç kategori
				eklenmemis...</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>