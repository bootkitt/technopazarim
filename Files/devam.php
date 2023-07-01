<?php !defined("index") ? die("hacking ?") : null;?>
<?php 
 $id = $_GET["id"];
 $urun = $db->prepare("select * from urunler inner join kategoriler on 

kategoriler.kategori_id = urunler.urun_kategori where urun_id=? and urun_durum=?");
 $urun->execute(array($id,1));
$x =  $urun->fetchALL(PDO::FETCH_ASSOC);

// urun hit bolumu
if(!@$_COOKIE["hit".$id]){
 $hit = $db->prepare("update urunler set urun_hit = urun_hit +1 where urun_id=?");
 $hit->execute(array($id));

 setcookie("hit".$id,"_",time ()+(60*60*24*30));
}
 
foreach($x as $m){
	
	$yorum = $db->prepare("select * from yorumlar where yorum_urun_id=? and yorum_onay=?");
	$yorum->execute(array($m["urun_id"],1));
	$yorum->fetchALL(PDO::FETCH_ASSOC);
	$x = $yorum->rowCount();
	
	?>
	<br />
	<br /><br /><a href="?do=devam&id=<?php echo $m["urun_id"];?>" class="btn choose popup">Satın Al</a>
	<br /><br /><br />
	<br /><br /><br /><br />
	<br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br /><br />
	<br /><br /><br /><br /><br /><br /><br /><br />
	<?php
	
}


 $yorum = $db->prepare("select * from yorumlar where yorum_urun_id=? and yorum_onay=?");
 $yorum->execute(array($id,1));
 $b = $yorum->fetchALL(PDO::FETCH_ASSOC);
 $x = $yorum->rowCount();
 
   if($x){
	   
     foreach($b as $m){
		 
		 ?>
		 <?php
		 
		 
		 
	 }	  
	
	if($_SESSION){
		
		?>
	<div style="font-size:19px;padding:10px;">mesaj gonder</div>
<div class="sol2">
   <form action="" method="post">
	<ul> 
	
	<li><input type="hidden"  value="<?php echo $_SESSION["uye"];?>" name="isim" /></li>
	
	<li><input type="hidden"  value="<?php echo $_SESSION["eposta"];?>" name="mail" /></li>
	
	
	<li><textarea name="mesaj" id="" cols="50" rows="10"></textarea></li>
	
	<li><button type="submit">gonder</button></li>
	</ul>
	</form>
	</div>
	<?php
		
		
	}else {
		
		?>
	<div style="font-size:19px;padding:10px;">mesaj gonder</div>
<div class="sol2">
   <form action="" method="post">
	<ul> 
	<li>adınız</li>
	<li><input type="text" name="isim" /></li>
	<li>email</li>
	<li><input type="text" name="mail" /></li>
	
	
	<li><textarea name="mesaj" id="" cols="50" rows="10"></textarea></li>
	
	<li><button type="submit">gonder</button></li>
	</ul>
	</form>
	</div>
	<?php
		
		
	}
	
	
	
}

?>




	