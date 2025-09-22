-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Anamakine: 127.0.0.1
-- Üretim Zamanı: 22 Eyl 2025, 21:35:20
-- Sunucu sürümü: 10.4.32-MariaDB
-- PHP Sürümü: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Veritabanı: `db`
--

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analiz_sepet_birakmalar`
--

CREATE TABLE `analiz_sepet_birakmalar` (
  `birakma_id` int(11) NOT NULL,
  `uye_id` int(11) DEFAULT NULL,
  `urun_id` int(11) NOT NULL,
  `eklenme_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `cikarma_tarihi` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `analiz_sepet_birakmalar`
--

INSERT INTO `analiz_sepet_birakmalar` (`birakma_id`, `uye_id`, `urun_id`, `eklenme_tarihi`, `cikarma_tarihi`) VALUES
(1, 1, 1, '2025-09-22 19:13:19', NULL),
(2, 1, 1, '2025-09-22 19:19:16', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analiz_urun_goruntulemeler`
--

CREATE TABLE `analiz_urun_goruntulemeler` (
  `goruntuleme_id` int(11) NOT NULL,
  `uye_id` int(11) DEFAULT NULL,
  `urun_id` int(11) NOT NULL,
  `goruntuleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `kalma_suresi` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `analiz_urun_goruntulemeler`
--

INSERT INTO `analiz_urun_goruntulemeler` (`goruntuleme_id`, `uye_id`, `urun_id`, `goruntuleme_tarihi`, `kalma_suresi`) VALUES
(1, 1, 3, '2025-09-22 19:11:22', NULL),
(2, 1, 3, '2025-09-22 19:11:26', NULL),
(3, 1, 1, '2025-09-22 19:13:17', NULL),
(4, 1, 1, '2025-09-22 19:13:58', NULL),
(5, 1, 1, '2025-09-22 19:19:15', NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `analiz_ziyaretler`
--

CREATE TABLE `analiz_ziyaretler` (
  `ziyaret_id` int(11) NOT NULL,
  `uye_id` int(11) DEFAULT NULL,
  `ip_adresi` varchar(45) NOT NULL,
  `kullanici_temsilcisi` text DEFAULT NULL,
  `giris_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `cikis_tarihi` timestamp NULL DEFAULT NULL,
  `kaynak` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `analiz_ziyaretler`
--

INSERT INTO `analiz_ziyaretler` (`ziyaret_id`, `uye_id`, `ip_adresi`, `kullanici_temsilcisi`, `giris_tarihi`, `cikis_tarihi`, `kaynak`) VALUES
(1, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:12', NULL, 'homepage'),
(2, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:18', NULL, 'homepage'),
(3, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:19', NULL, 'homepage'),
(4, NULL, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:20', NULL, 'homepage'),
(5, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:27', NULL, 'homepage'),
(6, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:35', NULL, 'homepage'),
(7, 1, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:26:22', NULL, 'homepage');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `bilet_mesajlari`
--

CREATE TABLE `bilet_mesajlari` (
  `mesaj_id` int(11) NOT NULL,
  `bilet_id` int(11) NOT NULL,
  `gonderen_id` int(11) NOT NULL,
  `mesaj_icerik` text NOT NULL,
  `mesaj_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `gonderen_tip` enum('uye','admin') DEFAULT 'uye'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `destek_biletleri`
--

CREATE TABLE `destek_biletleri` (
  `bilet_id` int(11) NOT NULL,
  `uye_id` int(11) NOT NULL,
  `bilet_baslik` varchar(200) NOT NULL,
  `bilet_icerik` text NOT NULL,
  `bilet_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `bilet_durum` enum('acik','kapali','beklemede') DEFAULT 'acik',
  `bilet_oncelik` enum('dusuk','orta','yuksek','acil') DEFAULT 'orta'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `dijital_stok`
--

CREATE TABLE `dijital_stok` (
  `stok_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `stok_kodu` text NOT NULL,
  `stok_durum` enum('aktif','kullanildi','iptal') DEFAULT 'aktif',
  `kullanilan_siparis_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `guvenlik_kayitlari`
--

CREATE TABLE `guvenlik_kayitlari` (
  `kayit_id` int(11) NOT NULL,
  `uye_id` int(11) DEFAULT NULL,
  `olay_tipi` varchar(50) NOT NULL,
  `aciklama` text DEFAULT NULL,
  `ip_adresi` varchar(45) DEFAULT NULL,
  `kullanici_temsilcisi` text DEFAULT NULL,
  `olay_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `guvenlik_kayitlari`
--

INSERT INTO `guvenlik_kayitlari` (`kayit_id`, `uye_id`, `olay_tipi`, `aciklama`, `ip_adresi`, `kullanici_temsilcisi`, `olay_tarihi`) VALUES
(1, 1, 'logout', 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:20'),
(2, 1, 'login_success', 'User logged in successfully', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', '2025-09-22 19:19:27');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `kategoriler`
--

CREATE TABLE `kategoriler` (
  `kategori_id` int(11) NOT NULL,
  `kategori_adi` varchar(200) NOT NULL,
  `kategori_aciklama` varchar(500) NOT NULL,
  `kategori_ust_id` int(11) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `kategoriler`
--

INSERT INTO `kategoriler` (`kategori_id`, `kategori_adi`, `kategori_aciklama`, `kategori_ust_id`) VALUES
(1, 'Windows Lisansları', 'Orijinal ve ömür boyu geçerli Windows sürümleri. (Windows 10, Windows 11 vb.)', 0),
(2, 'Office Lisansları', 'Microsoft Office ve alternatif ofis yazılımları (Office 2019, Office 2021, Office 365 vb.)', 0),
(3, 'Antivirüs & Güvenlik Yazılımları', 'Bilgisayarınızı ve verilerinizi koruyan güvenlik yazılımları. (Kaspersky, ESET, Norton vb.)', 0),
(4, 'Oyunlar & Oyun Kodları', 'Steam, Epic Games, Origin, Battle.net gibi platformlar için oyun kodları.', 0),
(5, 'Gift Card & Cüzdan Kodları', 'Steam, PlayStation, Xbox, Google Play, iTunes, Netflix gibi hediye kartları.', 0),
(6, 'Bilgisayar Donanımı', '???? Masaüstü ve dizüstü bilgisayarlar için gerekli parçalar.', 0),
(7, 'İşlemciler (CPU)', 'Intel ve AMD’nin en güncel işlemci modelleri.', 6),
(8, 'Ekran Kartları (GPU)', 'Oyun ve profesyonel grafik işleme için ekran kartları.', 6);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `odeme_ayarlari`
--

CREATE TABLE `odeme_ayarlari` (
  `id` int(11) NOT NULL,
  `ayar_adi` varchar(100) NOT NULL,
  `ayar_deger` text DEFAULT NULL,
  `aciklama` text DEFAULT NULL,
  `olusturulma_tarihi` timestamp NOT NULL DEFAULT current_timestamp(),
  `guncelleme_tarihi` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Tablo döküm verisi `odeme_ayarlari`
--

INSERT INTO `odeme_ayarlari` (`id`, `ayar_adi`, `ayar_deger`, `aciklama`, `olusturulma_tarihi`, `guncelleme_tarihi`) VALUES
(1, 'banka_adi', '', 'IBAN\'ın ait olduğu banka adı', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(2, 'hesap_sahibi', NULL, 'IBAN hesabının sahibi', '2025-09-19 18:51:54', '2025-09-20 12:13:51'),
(3, 'iban', 'TR92 0010 3000 0000 0057 6449 00', 'IBAN numarası', '2025-09-19 18:51:54', '2025-09-22 19:18:52'),
(4, 'swift_kodu', '', 'Banka için SWIFT/BIC kodu', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(5, 'sube_kodu', '', 'Banka şube kodu', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(6, 'hesap_no', '', 'Banka hesap numarası', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(7, 'para_birimi', 'TRY', 'Varsayılan para birimi', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(8, 'odeme_yontemi', 'bank_transfer', 'Varsayılan ödeme yöntemi', '2025-09-19 18:51:54', '2025-09-19 18:51:54'),
(9, 'aktif', '1', 'Ödeme yönteminin aktif durumu (1: aktif, 0: pasif)', '2025-09-19 18:51:54', '2025-09-20 12:13:56');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `sepet`
--

CREATE TABLE `sepet` (
  `sepet_id` int(11) NOT NULL,
  `uye_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `urun_adet` int(11) NOT NULL,
  `eklenme_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparisler`
--

CREATE TABLE `siparisler` (
  `siparis_id` int(11) NOT NULL,
  `uye_id` int(11) NOT NULL,
  `siparis_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `siparis_toplam` decimal(10,2) NOT NULL,
  `siparis_durum` enum('beklemede','tamamlandi','iptal','iade') DEFAULT 'beklemede',
  `odeme_durum` enum('beklemede','tamamlandi','basarisiz','iade') DEFAULT 'beklemede',
  `odeme_tipi` varchar(50) DEFAULT NULL,
  `shopier_order_id` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparis_notlari`
--

CREATE TABLE `siparis_notlari` (
  `id` int(11) NOT NULL,
  `siparis_id` int(11) NOT NULL,
  `not_tipi` varchar(50) NOT NULL,
  `not_icerik` text NOT NULL,
  `olusturan` varchar(50) NOT NULL,
  `olusturma_tarihi` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `siparis_urunler`
--

CREATE TABLE `siparis_urunler` (
  `siparis_urun_id` int(11) NOT NULL,
  `siparis_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `urun_adet` int(11) NOT NULL,
  `urun_fiyat` decimal(10,2) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `site_ayarlar`
--

CREATE TABLE `site_ayarlar` (
  `ayarlar` int(1) NOT NULL,
  `site_id` int(11) NOT NULL,
  `site_baslik` varchar(100) NOT NULL,
  `site_slogan` varchar(100) NOT NULL,
  `site_aciklama` varchar(100) NOT NULL,
  `site_keyw` varchar(1000) NOT NULL,
  `site_hakkimda` text NOT NULL,
  `site_yil` varchar(4) NOT NULL,
  `site_kayanyazi` varchar(100) NOT NULL,
  `ayarlayan` varchar(200) DEFAULT NULL,
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
  `urun_baslik` varchar(200) NOT NULL,
  `urun_fiyat` decimal(10,2) NOT NULL,
  `urun_aciklama` text NOT NULL,
  `urun_satinal_link` text NOT NULL,
  `urun_ekleyen` varchar(200) DEFAULT NULL,
  `urun_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `urun_kategori` int(11) NOT NULL DEFAULT 0,
  `urun_durum` int(11) NOT NULL DEFAULT 0,
  `urun_hit` int(11) DEFAULT 0,
  `urun_resim` varchar(300) DEFAULT NULL,
  `urun_resimler` text DEFAULT NULL,
  `urun_stok` int(11) DEFAULT 0,
  `urun_tip` enum('license','file','service') DEFAULT 'license'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `urunler`
--

INSERT INTO `urunler` (`urun_id`, `urun_baslik`, `urun_fiyat`, `urun_aciklama`, `urun_satinal_link`, `urun_ekleyen`, `urun_tarih`, `urun_kategori`, `urun_durum`, `urun_hit`, `urun_resim`, `urun_resimler`, `urun_stok`, `urun_tip`) VALUES
(1, 'Nvidia RTX 3070', 37000.00, 'NVIDIA GeForce RTX 3070, NVIDIA\'nın Ampere mimarisine dayanan ve Ekim 2020\'de piyasaya sürülen güçlü bir masaüstü grafik kartıdır. Oyuncular ve içerik üreticileri için yüksek performans sunan bu kart, özellikle 1440p çözünürlükte akıcı bir oyun deneyimi sağlamak üzere tasarlanmıştır.', '', NULL, '2025-09-22 18:27:52', 8, 1, 3, 'uploads/products/68d19528d73a6.jpg', NULL, 150, 'license'),
(2, 'Minecraft Java Kodu', 1499.00, '', '', NULL, '2025-09-22 18:29:10', 4, 1, 0, NULL, NULL, 999, 'license'),
(3, 'Minecraft Pocket Kodu', 1499.00, '', '', NULL, '2025-09-22 18:29:31', 4, 1, 2, NULL, NULL, 999, 'license');

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `uyeler`
--

CREATE TABLE `uyeler` (
  `uye_id` int(11) NOT NULL,
  `uye_adi` varchar(200) NOT NULL,
  `uye_sifre` varchar(200) NOT NULL,
  `uye_eposta` varchar(200) NOT NULL,
  `uye_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `uye_rutbe` int(11) NOT NULL DEFAULT 0,
  `uye_onay` int(11) NOT NULL DEFAULT 0,
  `uye_hakkimda` varchar(500) NOT NULL,
  `uye_2fa_enabled` tinyint(1) DEFAULT 0,
  `uye_2fa_secret` varchar(255) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Tablo döküm verisi `uyeler`
--

INSERT INTO `uyeler` (`uye_id`, `uye_adi`, `uye_sifre`, `uye_eposta`, `uye_tarih`, `uye_rutbe`, `uye_onay`, `uye_hakkimda`, `uye_2fa_enabled`, `uye_2fa_secret`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@gmail.com', '2016-08-26 22:36:50', 1, 1, '  ', 0, NULL);

-- --------------------------------------------------------

--
-- Tablo için tablo yapısı `yorumlar`
--

CREATE TABLE `yorumlar` (
  `yorum_id` int(11) NOT NULL,
  `uye_id` int(11) NOT NULL,
  `urun_id` int(11) NOT NULL,
  `yorum_baslik` varchar(200) NOT NULL,
  `yorum_icerik` text NOT NULL,
  `yorum_puan` int(1) NOT NULL DEFAULT 5,
  `yorum_tarih` timestamp NOT NULL DEFAULT current_timestamp(),
  `yorum_durum` int(1) NOT NULL DEFAULT 1
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

--
-- Dökümü yapılmış tablolar için indeksler
--

--
-- Tablo için indeksler `analiz_sepet_birakmalar`
--
ALTER TABLE `analiz_sepet_birakmalar`
  ADD PRIMARY KEY (`birakma_id`);

--
-- Tablo için indeksler `analiz_urun_goruntulemeler`
--
ALTER TABLE `analiz_urun_goruntulemeler`
  ADD PRIMARY KEY (`goruntuleme_id`);

--
-- Tablo için indeksler `analiz_ziyaretler`
--
ALTER TABLE `analiz_ziyaretler`
  ADD PRIMARY KEY (`ziyaret_id`);

--
-- Tablo için indeksler `bilet_mesajlari`
--
ALTER TABLE `bilet_mesajlari`
  ADD PRIMARY KEY (`mesaj_id`);

--
-- Tablo için indeksler `destek_biletleri`
--
ALTER TABLE `destek_biletleri`
  ADD PRIMARY KEY (`bilet_id`);

--
-- Tablo için indeksler `dijital_stok`
--
ALTER TABLE `dijital_stok`
  ADD PRIMARY KEY (`stok_id`);

--
-- Tablo için indeksler `guvenlik_kayitlari`
--
ALTER TABLE `guvenlik_kayitlari`
  ADD PRIMARY KEY (`kayit_id`);

--
-- Tablo için indeksler `kategoriler`
--
ALTER TABLE `kategoriler`
  ADD PRIMARY KEY (`kategori_id`),
  ADD KEY `idx_kategori_ust_id` (`kategori_ust_id`);

--
-- Tablo için indeksler `odeme_ayarlari`
--
ALTER TABLE `odeme_ayarlari`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ayar_adi` (`ayar_adi`);

--
-- Tablo için indeksler `sepet`
--
ALTER TABLE `sepet`
  ADD PRIMARY KEY (`sepet_id`);

--
-- Tablo için indeksler `siparisler`
--
ALTER TABLE `siparisler`
  ADD PRIMARY KEY (`siparis_id`);

--
-- Tablo için indeksler `siparis_notlari`
--
ALTER TABLE `siparis_notlari`
  ADD PRIMARY KEY (`id`);

--
-- Tablo için indeksler `siparis_urunler`
--
ALTER TABLE `siparis_urunler`
  ADD PRIMARY KEY (`siparis_urun_id`);

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
-- Tablo için indeksler `yorumlar`
--
ALTER TABLE `yorumlar`
  ADD PRIMARY KEY (`yorum_id`);

--
-- Dökümü yapılmış tablolar için AUTO_INCREMENT değeri
--

--
-- Tablo için AUTO_INCREMENT değeri `analiz_sepet_birakmalar`
--
ALTER TABLE `analiz_sepet_birakmalar`
  MODIFY `birakma_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `analiz_urun_goruntulemeler`
--
ALTER TABLE `analiz_urun_goruntulemeler`
  MODIFY `goruntuleme_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Tablo için AUTO_INCREMENT değeri `analiz_ziyaretler`
--
ALTER TABLE `analiz_ziyaretler`
  MODIFY `ziyaret_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Tablo için AUTO_INCREMENT değeri `bilet_mesajlari`
--
ALTER TABLE `bilet_mesajlari`
  MODIFY `mesaj_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `destek_biletleri`
--
ALTER TABLE `destek_biletleri`
  MODIFY `bilet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `dijital_stok`
--
ALTER TABLE `dijital_stok`
  MODIFY `stok_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `guvenlik_kayitlari`
--
ALTER TABLE `guvenlik_kayitlari`
  MODIFY `kayit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `kategoriler`
--
ALTER TABLE `kategoriler`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Tablo için AUTO_INCREMENT değeri `odeme_ayarlari`
--
ALTER TABLE `odeme_ayarlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Tablo için AUTO_INCREMENT değeri `sepet`
--
ALTER TABLE `sepet`
  MODIFY `sepet_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `siparisler`
--
ALTER TABLE `siparisler`
  MODIFY `siparis_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `siparis_notlari`
--
ALTER TABLE `siparis_notlari`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `siparis_urunler`
--
ALTER TABLE `siparis_urunler`
  MODIFY `siparis_urun_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Tablo için AUTO_INCREMENT değeri `site_ayarlar`
--
ALTER TABLE `site_ayarlar`
  MODIFY `site_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Tablo için AUTO_INCREMENT değeri `urunler`
--
ALTER TABLE `urunler`
  MODIFY `urun_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Tablo için AUTO_INCREMENT değeri `uyeler`
--
ALTER TABLE `uyeler`
  MODIFY `uye_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Tablo için AUTO_INCREMENT değeri `yorumlar`
--
ALTER TABLE `yorumlar`
  MODIFY `yorum_id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
