<!DOCTYPE html>
<html >
  <head>
    <meta charset="UTF-8">
    <title>Satın Alma Formu</title>
    
    
    
    
        <link rel="stylesheet" href="css/style.css">

    
    
    
  </head>

  <body>

    <!-- 
  ****************************************
  Contest Entry for Treehouse:
  "Design a Contact Form"
  Submitted by Lisa Wagner
  ****************************************
-->

<div id="contact-form">
	<div>
		<h1>Satın Alma Formu</h1> 
		<h4>Lütfen Tüm Bilgileri Doğru Giriniz.</h4>
	</div>
		<p id="failure">Oppssss ... mesaj gönderilmedi .</p>  
		<p id="success">Mesajınız başarıyla gönderildi.Teşekkür ederiz</p>

		   <form method="POST" action="iletisim.php">
    <p class="input-block">
        <label for="contact_author">
            <strong>Ad-Soyad:</strong> *</label>
        <input type="text" name="contact_author" id="contact_author" placeholder="Ad ve Soyad" required="required">
    </p>
    <p class="input-block">
        <label for="_replyto">
            <strong>E-Mail:</strong> *</label>
        <input type="email" name="_replyto" id="_replyto" placeholder="Mail Adresiniz Lütfen Doğru Giriniz" required="required">
    </p>            
    <p class="input-block">
        <label for="contact_subject">
            <strong>Satın Aldığınız Ürün:</strong></label>
        <select id="subject" name="subject">   
			         <option>PUBG</option>
			         <option>Gta 5</option>  
			         <option>Minecraft</option>
		</select>
    </p>
    <p class="textarea-block">
        <label for="contact_content">
            <strong>Mesajınız:</strong> *</label>
        <textarea name="contact_content" id="contact_content" placeholder="Mesajınızı Girin" required="required"></textarea>
    </p>
    <input type="hidden" name="_next" value="//domainadresimiz.com/tesekkurler.html"> <!-- Form forms.brace.io adresine gönderildikten sonra tekrar sitenize dönüş yapılarak sitenizdeki tesekkurler.html dosyasının açılmasını sağlıyor. -->
    <div>		           
		<button name="submit" type="submit" id="submit" >Gönder</button> 
	</div>
</form>

	</div>
    
    
    
    
    
  </body>
</html>
