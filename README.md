# Proje Finans Yönetimi Uygulaması

## Genel Özet

Proje Finans Yönetimi uygulaması, Laravel ve Filament kullanılarak geliştirilmiş kapsamlı bir finans yönetim sistemidir. Aşağıda projenin modül bazında genel bir özeti bulunmaktadır:

## Şirket Yönetimi (Company Management)

- **Şirketler (Companies)**: Sistemde kayıtlı şirketlerin yönetimi
  - Şirket bilgileri (isim, e-posta, telefon)
  - İletişim bilgileri ve adres yönetimi
  - Şirket bazlı izin ve yetkilendirme sistemi

## Proje Yönetimi (Project Management)

- **Projeler (Projects)**: Şirketlere bağlı projelerin yönetimi
  - Proje zaman çizelgesi (başlangıç ve bitiş tarihleri)
  - Proje sorumlusu atama
  - Proje durumu takibi (aktif/pasif)
- **Görevler (Tasks)**: Projelere bağlı görevlerin yönetimi
  - Görev atama ve takip sistemi
  - Görev durumu ve öncelik yönetimi

## Muhasebe Yönetimi (Accounting)

- **Hesaplar (Accounts)**: Finansal hesapların yönetimi
  - Hesap bakiyesi takibi
  - Hesap grupları ile kategorilendirme
  - Tek Düzen Hesap Planı entegrasyonu
- **İşlemler (Transactions)**: Finansal işlemlerin kaydı ve takibi
  - İşlem grupları ve kategorilendirme
  - Gelir-gider takibi

## Kullanıcı Yönetimi (User Management)

- **Kullanıcılar (Users)**: Sistem kullanıcılarının yönetimi
- **Roller ve İzinler (Roles & Permissions)**: Yetkilendirme sistemi
  - Rol bazlı erişim kontrolü
  - Özelleştirilmiş izin yönetimi

## Dashboard ve Raporlama

- **Widget'lar**: Son şirketler, projeler ve işlemler için özet görünümler
- Finansal durum özeti
- Proje ve görev durumu takibi

## Teknoloji ve Özellikler

Uygulama, Filament admin paneli kullanarak modern ve kullanıcı dostu bir arayüz sunmaktadır. Çok dilli destek ile farklı dillerde kullanılabilir şekilde tasarlanmıştır. Ayrıca, Docker ile konteynerize edilmiş bir yapıya sahiptir, bu da kolay kurulum ve dağıtım sağlar.

## Veri İlişkileri

Her modül, ilişkisel bir veritabanı yapısı ile birbirine bağlanmış durumdadır. Örneğin, projeler şirketlere, görevler projelere, hesaplar kullanıcılara bağlıdır. Bu yapı, kapsamlı bir finans ve proje yönetimi ekosistemi oluşturur.