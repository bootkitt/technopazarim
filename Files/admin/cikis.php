<?php !defined("admin") ? die("hacking ?") : null;?>
<div class="admin-icerik-sag"> 
<h2>Çıkış Yap</h2>
<?php

session_destroy();
 
 echo '<div class="hata">Başarıyla çıkış yaptınız.. Anasayfaya yönlendiriliyorsunuz...</div>';
 
 header("refresh: 2; url=giris.php");
 
?>
</div>