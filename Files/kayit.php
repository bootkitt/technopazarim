
<?php !defined("index") ? die("hacking ?") : null;?>
<?php 


  if(!$_SESSION){
	  
	  
	  if($_POST){
		  
		  $isim =     strip_tags(trim($_POST["isim"]));
		  $sifre =    strip_tags(trim($_POST["sifre"]));
		  $email =    trim($_POST["email"]);
		  $hakkimda = strip_tags($_POST["hakkimda"],"<img>");
		  
		  if(!$isim || !$sifre || !$email){
			  
			  echo '<div class="hata">gerekli alanları doldurmanız gerekiyor</div>';
			  
		  }elseif (!filter_var($email,FILTER_VALIDATE_EMAIL)){
			  
			  
			  echo '<div class="hata">bu mail adresi gecerli deyil baska bir tane deneyin</div>';
			  
		  }else {
			  
			  $sifre = md5($sifre);
			  
			  
			 $v = $db->prepare("select * from uyeler where uye_adi=?");
             $v->execute(array($isim));
             $x = $v->fetch(PDO::FETCH_ASSOC);
             $y = $v->rowCount();			 
			 
            if($y){
				
				echo '<div class="hata"><span style="color:red;">'.$x["uye_adi"].'</span> adlı uye sistemde zaten var baska bir tane deneyin</div>';
				
			}else {
				
				
				$x = $db->prepare("insert into uyeler set 
				
				          uye_adi=?,
						  uye_sifre=?,
						  uye_eposta=?,
						  uye_hakkimda=?
				
				");
			$kayit =	$x->execute(array($isim,$sifre,$email,$hakkimda));
			
			  if($kayit){
				  
				echo '<div class="basarili2">basarıyla kayıt oldunuz uyeliğinizle giris yapabilirsiniz</div>';  
				  
				  header("refresh: 2; url=index.php");
				  
			  }else {
				  
				  echo '<div class="hata">uye olurken bir hata olustu</div>';
				  
			  }
			
				
			}			 
			  
		  }
		  
		  
	  }else {
		 
       ?>
     <div class="sol2">
		<h2>kayıt formu</h2>
  <form action="" method="post">	
		<ul> 
		<li>adınız</li>
		<li><input type="text" name="isim" /></li>
		<li>sifre</li>
		<li><input type="text" name="sifre" placeholder="sifrenizi girin" /></li>
		<li>email</li>
		<li><input type="text" name="email" /></li>
		<li>hakkımda</li>
		<li><textarea name="hakkimda" id="" cols="50" rows="10"></textarea></li>
	
		<li><button type="submit">gonder</button></li>
		</ul>
		</form>	
	</div>
       <?php	   
		  
		  
		  
	  }
	  
	  
  }


?>