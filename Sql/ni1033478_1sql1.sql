-- phpMyAdmin SQL Dump
-- version 4.6.6
-- https://www.phpmyadmin.net/
--
-- Anamakine: localhost
-- Üretim Zamanı: 25 Haz 2018, 16:48:39
-- Sunucu sürümü: 5.5.60-0+deb7u1
-- PHP Sürümü: 5.6.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `ni1033478_1sql1`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `kategori_id` int(11) NOT NULL,
  `kategori_adi` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `kategori_aciklama` varchar(500) COLLATE utf8_turkish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`kategori_id`, `kategori_adi`, `kategori_aciklama`) VALUES
(1, 'Oyun', 'Oyunlar'),
(7, 'Program', 'Programlar ');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_ayarlar`
--

CREATE TABLE `site_ayarlar` (
  `ayarlar` int(1) NOT NULL,
  `site_id` int(11) NOT NULL,
  `site_baslik` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `site_slogan` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `site_aciklama` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `site_keyw` varchar(1000) COLLATE utf8_turkish_ci NOT NULL,
  `site_hakkimda` text COLLATE utf8_turkish_ci NOT NULL,
  `site_yil` varchar(4) COLLATE utf8_turkish_ci NOT NULL,
  `site_kayanyazi` varchar(100) COLLATE utf8_turkish_ci NOT NULL,
  `ayarlayan` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `site_durum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `site_ayarlar`
--

INSERT INTO `site_ayarlar` (`ayarlar`, `site_id`, `site_baslik`, `site_slogan`, `site_aciklama`, `site_keyw`, `site_hakkimda`, `site_yil`, `site_kayanyazi`, `ayarlayan`, `site_durum`) VALUES
(1, 1, 'TechnoPazarim', 'Oyunun Adresi', 'TechnoPazarim Oyunun Adresi ', 'technopazarim', 'Kurulduğumuz ilk andan itibaren Türkiye’deki Oyun Hesapları sektöründe sağlam adımlarla ilerlemeyi, üyelerimize en kaliteli hizmeti sunmayı amaçlayan profesyonel ekibimizle sektöre ilk adımlarımızı attık. Yüksek güvenlikli alışveriş sistemimiz ile size profesyonel bir hizmet sunmaktayız. aynı zamanda 7/24 Kaliteli bir hizmet veren uzman kadromuz sizlere her konuda yardımcı olacak profesyonel destek verecek görevlilerimiz ile seninle ve diğer üyelerimiz ile beraber bire bir görüşemekten sorunlarınızı çözmekten sizlere en kaliteli desteği vermekten Mutluluk Duyuyoruz. TechnoPazarim Ekibi', '2018', '<b>%100 Güvenli</b> <b class=\"is-visible\">Ucuz, Bütce Dostu</b> <b>1000+ Mutlu Müşteri</b>        ', 'admin', 1);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `urunler`
--

CREATE TABLE `urunler` (
  `urun_id` int(11) NOT NULL,
  `urun_baslik` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `urun_fiyat` int(11) NOT NULL,
  `urun_aciklama` text COLLATE utf8_turkish_ci NOT NULL,
  `urun_satinal_link` text COLLATE utf8_turkish_ci NOT NULL,
  `urun_ekleyen` varchar(200) COLLATE utf8_turkish_ci DEFAULT NULL,
  `urun_tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `urun_kategori` int(11) NOT NULL DEFAULT '0',
  `urun_durum` int(11) NOT NULL DEFAULT '0',
  `urun_hit` int(11) DEFAULT '0',
  `urun_resim` varchar(300) COLLATE utf8_turkish_ci DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`urun_id`, `urun_baslik`, `urun_fiyat`, `urun_aciklama`, `urun_satinal_link`, `urun_ekleyen`, `urun_tarih`, `urun_kategori`, `urun_durum`, `urun_hit`, `urun_resim`) VALUES
(45, 'FİFA 16', 30, 'Tum Herşeyi Değişir! | Email : Sifre Verilir!  ', ' https://shopier.com/683611', 'admin', '2018-06-05 09:52:55', 1, 1, 0, NULL),
(48, 'FALLOUT 4', 30, 'Tum Herşeyi Değişir! | Email : Sifre Verilir!', 'https://www.shopier.com/684907', 'admin', '2018-06-05 10:00:32', 1, 1, 0, NULL),
(47, 'FİFA 18', 40, 'Tum Herşeyi Değişir! | Email : Sifre Verilir!  ', 'http://www.shopier.com/682478', 'admin', '2018-06-05 09:58:48', 1, 1, 0, NULL),
(49, 'A WAY OUT', 40, 'Tum Herşeyi Değişir! | Email : Sifre Verilir! ', 'https://www.shopier.com/678785', 'admin', '2018-06-05 10:01:04', 1, 1, 0, NULL),
(46, 'FİFA 17', 35, 'Tum Herşeyi Değişir! | Email : Sifre Verilir!   ', ' http://www.shopier.com/680846', 'admin', '2018-06-05 09:56:09', 1, 1, 0, NULL),
(50, 'STEAM RANDOM KEY', 3, 'Steam Key Verilir! | Steamda Aktifleştirilir. ', 'https://www.shopier.com/684678', 'admin', '2018-06-05 10:05:07', 1, 1, 0, NULL),
(51, 'FAR CRY PRİMAL', 10, 'UbiSoft Hesabi Verilir! | Email : Sifre Verilir! ', 'https://www.shopier.com/685059', 'admin', '2018-06-05 10:07:16', 1, 1, 0, NULL),
(52, 'FAR CRY 5', 30, 'UbiSoft Hesabi Verilir! | Email : Sifre Verilir!', 'https://www.shopier.com/682039', 'admin', '2018-06-05 10:08:36', 1, 1, 0, NULL),
(53, 'Battlefield 1', 25, 'Origin Hesabi Verilir! | Email : Sifre Verilir!', 'https://www.shopier.com/679604', 'admin', '2018-06-05 10:09:37', 1, 1, 0, NULL),
(54, 'PlayerUnknown\'s BattleGrounds', 40, 'Tum Herşeyi Değişir | Steam Hesap Verilir.  ', 'https://www.shopier.com/684755', 'admin', '2018-06-05 10:11:29', 1, 1, 0, NULL),
(55, 'GTA 5', 30, 'Hesap Full Erişim! | Rockstar Hesabı Verilir.  ', 'http://www.shopier.com/679812', 'admin', '2018-06-05 10:12:48', 1, 1, 0, NULL),
(56, 'Minecraft Pelerin', 20, 'Hesap Full Erişim | Optifine Hesap Verilir.', 'https://www.shopier.com/678955', 'admin', '2018-06-05 10:14:40', 1, 1, 0, NULL),
(57, 'MineCraft Elmas Paket', 30, 'Email Erişim! | Mojang Hesabı Verilir. ', 'http://www.shopier.com/685287', 'admin', '2018-06-05 10:15:23', 1, 1, 0, NULL),
(58, 'MineCraft Altin Paket', 15, 'Email Değişmez! Skin Nick Sifre Degişir!\r\n', 'http://www.shopier.com/678772', 'admin', '2018-06-05 10:16:34', 7, 1, 0, NULL),
(59, 'MineCraft Demir Paket', 3, 'Hesap Hiçbir Erişim Yoktur!', 'https://www.shopier.com/680745', 'admin', '2018-06-05 10:18:06', 7, 1, 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `uyeler`
--

CREATE TABLE `uyeler` (
  `uye_id` int(11) NOT NULL,
  `uye_adi` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `uye_sifre` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `uye_eposta` varchar(200) COLLATE utf8_turkish_ci NOT NULL,
  `uye_tarih` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `uye_rutbe` int(11) NOT NULL DEFAULT '0',
  `uye_onay` int(11) NOT NULL DEFAULT '0',
  `uye_hakkimda` varchar(500) COLLATE utf8_turkish_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `uyeler`
--

INSERT INTO `uyeler` (`uye_id`, `uye_adi`, `uye_sifre`, `uye_eposta`, `uye_tarih`, `uye_rutbe`, `uye_onay`, `uye_hakkimda`) VALUES
(42, 'admin', '5377f3f3d686eb3bd37b528d62be1281', 'admin@gmail.com', '2016-08-26 22:36:50', 1, 1, '  ');

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Tablo için indeksler `site_ayarlar`
--
ALTER TABLE `site_ayarlar`
  ADD PRIMARY KEY (`site_id`);

--
-- Tablo için indeksler `urunler`
--
ALTER TABLE `urunler`
  ADD PRIMARY KEY (`urun_id`);

--
-- Tablo için indeksler `uyeler`
--
ALTER TABLE `uyeler`
  ADD PRIMARY KEY (`uye_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- Tablo için AUTO_INCREMENT değeri `site_ayarlar`
--
ALTER TABLE `site_ayarlar`
  MODIFY `site_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `urun_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;
--
-- Tablo için AUTO_INCREMENT değeri `uyeler`
--
ALTER TABLE `uyeler`
  MODIFY `uye_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
