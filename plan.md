
---

### **Genel Tasarım Felsefesi ve Yapısı**

Tüm kullanıcılar için tutarlı bir deneyim sunmak adına genel bir sayfa yapısı oluşturalım:

1.  **Sol Navigasyon Menüsü:** Kullanıcının rolüne göre dinamik olarak değişen, ana modüllere (Ana Ekran, Projeler, Talepler, Şirketler, Kullanıcılar, Ödemeler vb.) hızlı erişim sağlayan sabit bir menü.
2.  **Üst Bar (Header):** Kullanıcı profili, bildirimler, ayarlar ve belki bir genel arama çubuğu içerir.
3.  **Ana İçerik Alanı:** Seçilen menü öğesine göre içeriğin görüntülendiği ana bölüm.

---

### **1. Ana Ekran Görünümü (Dashboard) Tasarımı**

Dashboard, her kullanıcının sisteme girdiğinde ilk gördüğü ve kendi rolü için en önemli bilgilere hızla ulaşabildiği yer olmalıdır. Bunu "kart" tabanlı modüler bir tasarımla yapabiliriz.

**Super Admin Dashboard:**
* **Düzen:** 4'lü veya 6'lı kart yapısı.
* **Kartlar:**
    * **Genel Bakış Kartları:**
        * "Toplam Şirket Sayısı"
        * "Aktif Proje Sayısı"
        * "Bu Hafta Biten Talep Sayısı"
        * "Ödenmemiş Toplam Tutar"
    * **Grafik Panelleri:**
        * **Aylık Proje Oluşturma Grafiği:** Son 6 ayda oluşturulan proje sayısını gösteren bir çubuk grafik.
        * **Ödeme Durumları Grafiği:** Ödenmiş, beklemede ve gecikmiş ödemeleri gösteren bir pasta (pie) chart.
    * **Hızlı Erişim Listesi:**
        * "Onay Bekleyen Son 5 Kullanıcı"
        * "Son Eklenen Şirketler"

**Şirket Sahibi Dashboard:**
* **Düzen:** Daha odaklı bir kart ve liste yapısı.
* **Kartlar:**
    * **Proje Durum Kartları:**
        * "Toplam Proje Sayısı"
        * "Devam Eden Projeler"
        * "Tamamlanan Projeler"
    * **Finansal Özet Kartları:**
        * "Toplam Hakediş"
        * "Ödenen Tutar"
        * "Kalan Bakiye"
* **Ana İçerik:**
    * **Projelerim Tablosu:** Proje adı, proje sorumlusu, ilerleme durumu (progress bar ile gösterilebilir), bitiş tarihi ve bütçe gibi temel bilgileri içeren bir tablo. Her satırda "Detayları Gör" butonu bulunur.
    * **Ekip Faaliyetleri:** Şirket çalışanlarının son aktiviteleri (örn: "Ahmet Yılmaz, 'X Projesi' için yeni bir talep tamamladı.").

**Proje Sorumlusu Dashboard:**
* **Düzen:** Tamamen proje odaklı.
* **Üst Bilgi Alanı:** Proje adı, ilerleme yüzdesi ($$\%$$), başlangıç/bitiş tarihleri ve bütçe gibi proje özet bilgileri.
* **Ana İçerik (Sekmeli Yapı - Tabbed Interface):**
    * **Sekme 1: Talepler (Kanban Board):** Talepleri "Yapılacak", "Yapılıyor", "Tamamlandı" ve "Gözden Geçirilecek" gibi sütunlarda kartlar halinde gösteren bir kanban panosu. Bu, görev takibini çok görsel ve sezgisel hale getirir. Her kartta talep adı, atanan kişi ve teslim tarihi yer alır.
    * **Sekme 2: Zaman Çizelgesi (Timeline):** Taleplerin başlangıç ve bitiş tarihlerini gösteren bir Gantt şeması görünümü. Geciken görevler kırmızı ile işaretlenir.
    * **Sekme 3: Bütçe & Ödemeler:** Projeye ait ödemelerin listesi, toplam harcama ve kalan bütçe.

**Talep Sahibi (Çalışan) Dashboard:**
* **Düzen:** Çok basit ve görev odaklı.
* **Ana İçerik:**
    * **"Bana Atanan Talepler" Listesi:** Net bir liste. Her satırda:
        * Talep Adı
        * Ait Olduğu Proje
        * **Teslim Tarihi (Geri sayım ile: "3 gün kaldı", "Gecikti: 2 gün")**
        * Durum (örn: "Yapılıyor")
        * Durumu değiştirmek için bir "Durum Değiştir" butonu.

---

### **2. Yeni Proje Ekleme Akışı Tasarımı**

Bu akışı adım adım ilerleyen bir sihirbaz (wizard) arayüzü ile tasarlamak, kullanıcı hatasını en aza indirir.

**"Yeni Proje Oluştur" Butonuna Tıklandığında Açılan Pencere/Sayfa:**

* **Adım 1: Şirket Seçimi**
    * Mevcut şirketler arasından bir arama kutusu ile seçim yapma.
    * Eğer şirket yoksa, yanında beliren bir `+ Yeni Şirket Oluştur` butonu ile hızlıca yeni şirket ekleme formu açılır.

* **Adım 2: Proje Detayları**
    * Proje Adı (zorunlu alan)
    * Proje Açıklaması
    * Başlangıç ve Bitiş Tarihleri (takvimden seçilir)
    * Proje Sorumlusu (Şirkete kayıtlı kullanıcılar arasından seçilir)

* **Adım 3: Prim Modeli Belirleme**
    * Bu adım kritik. Kullanıcıya net seçenekler sunulmalı:
        * **"Bu proje için prim nasıl hesaplansın?"** başlığı.
        * **Seçenek 1 (Radio Button): Proje Bazlı Prim**
            * Yanında bir yüzde ($$\%$$) giriş alanı belirir: "Tüm talepler için prim oranı: [ ___ ] %"
        * **Seçenek 2 (Radio Button): Talep Bazlı Prim**
            * "Prim oranı her talep oluşturulurken ayrı ayrı belirlenecektir." bilgilendirme metni.
        * **Not:** Kullanıcı bazlı prim, kullanıcının profilinde tanımlı olacağı için burada bir ayar yapmaya gerek olmayabilir. Sistem bunu otomatik olarak hesaba katacaktır.

* **Adım 4: Özet ve Onay**
    * Girilen tüm bilgilerin (Şirket, Proje Adı, Sorumlu, Prim Modeli vb.) bir özet listesi gösterilir.
    * `Projeyi Oluştur` butonu ile işlem tamamlanır.

---

### **3. Prim Hesaplama Kurallarının Kullanıcıya Gösterimi**

Bu karmaşık yapı, arayüzde olabildiğince basit gösterilmelidir. Özellikle ödeme veya hakediş raporlarında şeffaflık önemlidir.

**Prim Detayı Gösterimi (Örn: Bir ödeme raporunda):**

Bir kullanıcının bir aydaki toplam primini gösterirken, sadece sonucu değil, detayı da sunun:

**Ahmet Yılmaz - Temmuz 2025 Prim Hakedişi: ₺850** `(Detayları Göster)`

`(Detayları Göster)` linkine tıklandığında açılan bölüm:

| Talep Adı | Talep Tutarı | Prim Kaynağı | Oran | Kazanç |
| :--- | :--- | :--- | :--- | :--- |
| Logo Tasarımı | ₺2.000 | Talep Bazlı | %10 | ₺200 |
| Web Sitesi Ana Sayfa | ₺3.000 | Proje Bazlı (X Projesi) | %5 | ₺150 |
| Kullanıcı Profili | ₺4.000 | Kullanıcı Bazlı | %12.5 | ₺500 |
| **Toplam** | | | | **₺850** |

Bu tablo, primin nereden ve nasıl geldiğini net bir şekilde açıklar.

---

### **4. Süre Takibi ve Görev Yönetimi**

**Talep Detay Sayfası:**

* **Bilgi Paneli (Sağ veya Sol Sütun):**
    * **Proje:** [Proje Adı]
    * **Atanan Kişi:** [Kullanıcı Adı]
    * **Durum:** [Dropdown ile seçilebilir: Yapılacak, Yapılıyor, Tamamlandı]
    * **Teslim Tarihi:** [Tarih]
* **Süre Takibi Bölümü:**
    * **Tahmini Süre:** [örn: 16 saat] (Proje sorumlusu girer)
    * **Harcanan Süre:** [ `Zamanlayıcıyı Başlat/Durdur` butonu veya manuel giriş alanı ]
    * Bir ilerleme çubuğu (progress bar) ile `Harcanan Süre / Tahmini Süre` görselleştirilebilir.

**Kullanıcı Bazlı Görev Listesi (Talep Sahibinin Ekranı):**

* "Taleplerim" sayfası.
* Filtreleme seçenekleri: [Tümü, Gecikenler, Bu Hafta Bitecekler, Tamamlananlar]
* Geciken taleplerin satırları belirgin bir şekilde (örn: açık kırmızı arka plan) renklendirilir.
* 

**Loglama:**
* Tüm yapılan işlemler loglanmalı
* Örneğin taleplerin, projelerin veya ödemelerin listelendiği sayfada bir talebin hangi tarihte kim tarafından hangi alanın güncellendiğin gösteren bir log sayfasını pop up şeklinde açan ve en son işlem üstte olacak şekilde listeleyen bir yapı olmalı


Bu tasarım önerileri, sağladığınız teknik yapıyı son kullanıcı için anlaşılır, verimli ve görsel olarak tatmin edici bir deneyime dönüştürmeyi hedefler.
