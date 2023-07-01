<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=ISO-8859-1" />
	<title></title>
</head>
<body>
<?php
$to = "coderp2007@gmail.com";
$subject = "İletişim Formu";


$headers = "From:" . $_POST["contact_author"];
$headers .= "<" . $_POST["_replyto"] . ">\r\n";
$headers .= "Reply-To: " . $_POST["_replyto"] . "\r\n";
$headers .= "Return-Path: " . $_POST["_replyto"];

$message = "Sitenizden gelen iletişim form işlem sonucu\n\n";
$message .= "Adı-Soyadı: " . $_POST["contact_author"] . "\r\n";
$message .= "E-Mail: " . $_POST["_replyto"] . "\r\n";
$message .= "Konu: " . $_POST["subject"] . "\r\n";
$message .= "Mesaj: " . $_POST["contact_content"] . "\r\n";

$mail_kontrol=mail($to, $subject, $message, $headers);

if ($mail_kontrol) {echo "<h1><font color='green'>Mesaj yollama başarılı.Ana sayfaya yönlendiriliyorsunuz...</font></h1>"; echo '<meta http-equiv="refresh" content="3;URL=http://google.com.tr">';}
else {echo "<h1><font color='red'>Mesaj Yollama hatası.</font></h1>";}
?>
</body>
</html>