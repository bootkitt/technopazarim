<?php !defined("admin") ? die("hacking ?") : null;?>
<?php 

$v = $db->prepare("select * from sabit_sayfalar  order by sayfa_id desc limit 20");
$v->execute(array());
$k = $v->fetchALL(PDO::FETCH_ASSOC);
$x = $v->rowCount();



?>

<div class="admin-icerik-sag"> 
			<h2>sabit sayfalar <span style="float:right;"><a href="/admin/?do=sayfa_ekle">sayfa ekle</a></span></h2>
			<div class="konular"> 
			<table cellspacing="0" cellpadding="0"> 
			<thead> 
			<tr> 
			<th width="300">sayfa adi</th> <th width="500">sayfa acıklaması</th>
			 <th width="250">tarih</th>
			<th width="200">işlemler</th>
			</tr>
			</thead>
			<?php 
			 if($x){
				
                foreach($k as $m){
					?>
					<tbody> 
					<tr> 
					<td><?php echo $m["sayfa_adi"];?></td> <td><?php echo mb_substr($m["sayfa_aciklama"],0,40);?></td>
					
					<td><?php echo $m["sayfa_tarih"];?></td> 
					<td><span style="margin-left:35px;">
					<a href="/admin/?do=sayfa_duzenle&id=<?php echo $m["sayfa_id"];?>">duzenle</a></span> <span style="margin-left:10px;"><a  onclick="return confirm('sayfayı silmek istediğinize eminmisiniz..')" href="/admin/?do=sayfa_sil&id=<?php echo $m["sayfa_id"];?>">sil</a></span></td>
					</tr>
					</tbody>
					<?php
					
					
				}				
				 
			 }else {
				 
				echo '<tr><td colspan="5"><div class="hata">henuz hiç sayfa
				eklenmemis...</div></td></tr>'; 
				 
			 }
			
			?>
			</table>
			</div>
			</div>