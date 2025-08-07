<?php

namespace Database\Seeders;

use App\Models\HelpDocument;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class HelpDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing help documents to avoid duplicates
        HelpDocument::truncate();
        
        // Getting Started Category
        $this->createHelpDocument(
            title_en: 'Getting Started with Finance Management',
            title_tr: 'Finans Yönetimine Başlarken',
            content_en: '<h2>Welcome to the Finance Management System</h2>
                <p>This guide will help you get started with using the Finance Management application. The system allows you to manage your company finances, projects, tasks, and transactions efficiently.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Company management</li>
                    <li>Project tracking</li>
                    <li>Task management with payment tracking</li>
                    <li>Financial transaction recording</li>
                    <li>Account management</li>
                </ul>
                <p>To begin, navigate through the sidebar menu to access different sections of the application.</p>',
            content_tr: '<h2>Finans Yönetim Sistemine Hoş Geldiniz</h2>
                <p>Bu rehber, Finans Yönetimi uygulamasını kullanmaya başlamanıza yardımcı olacaktır. Sistem, şirket finanslarınızı, projelerinizi, görevlerinizi ve işlemlerinizi verimli bir şekilde yönetmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Şirket yönetimi</li>
                    <li>Proje takibi</li>
                    <li>Ödeme takibi ile görev yönetimi</li>
                    <li>Finansal işlem kaydı</li>
                    <li>Hesap yönetimi</li>
                </ul>
                <p>Başlamak için, uygulamanın farklı bölümlerine erişmek için kenar çubuğu menüsünde gezinin.</p>',
            slug: 'getting-started',
            category: 'Getting Started',
            order: 1
        );

        $this->createHelpDocument(
            title_en: 'User Interface Overview',
            title_tr: 'Kullanıcı Arayüzü Genel Bakış',
            content_en: '<h2>Understanding the User Interface</h2>
                <p>The Finance Management application has a clean and intuitive interface designed for ease of use.</p>
                <h3>Main Components</h3>
                <ul>
                    <li><strong>Navigation Sidebar:</strong> Access all main sections of the application</li>
                    <li><strong>Top Bar:</strong> Contains user profile, notifications, and language selection</li>
                    <li><strong>Main Content Area:</strong> Displays the selected section\'s content</li>
                    <li><strong>Action Buttons:</strong> Perform actions like create, edit, delete</li>
                </ul>
                <p>The interface is responsive and works well on both desktop and mobile devices.</p>',
            content_tr: '<h2>Kullanıcı Arayüzünü Anlamak</h2>
                <p>Finans Yönetimi uygulaması, kullanım kolaylığı için tasarlanmış temiz ve sezgisel bir arayüze sahiptir.</p>
                <h3>Ana Bileşenler</h3>
                <ul>
                    <li><strong>Gezinme Kenar Çubuğu:</strong> Uygulamanın tüm ana bölümlerine erişim</li>
                    <li><strong>Üst Çubuk:</strong> Kullanıcı profili, bildirimler ve dil seçimi içerir</li>
                    <li><strong>Ana İçerik Alanı:</strong> Seçilen bölümün içeriğini görüntüler</li>
                    <li><strong>Eylem Düğmeleri:</strong> Oluşturma, düzenleme, silme gibi eylemleri gerçekleştirin</li>
                </ul>
                <p>Arayüz duyarlıdır ve hem masaüstü hem de mobil cihazlarda iyi çalışır.</p>',
            slug: 'user-interface-overview',
            category: 'Getting Started',
            order: 2
        );

        // Companies Category
        $this->createHelpDocument(
            title_en: 'Managing Companies',
            title_tr: 'Şirketleri Yönetme',
            content_en: '<h2>Company Management</h2>
                <p>The Companies section allows you to manage all your company information in one place.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Create new companies with detailed information</li>
                    <li>Edit existing company details</li>
                    <li>View company financial summaries</li>
                    <li>Manage company contacts and addresses</li>
                </ul>
                <h3>Creating a New Company</h3>
                <ol>
                    <li>Navigate to the Companies section in the sidebar</li>
                    <li>Click the "New Company" button</li>
                    <li>Fill in the required information</li>
                    <li>Click "Save" to create the company</li>
                </ol>',
            content_tr: '<h2>Şirket Yönetimi</h2>
                <p>Şirketler bölümü, tüm şirket bilgilerinizi tek bir yerde yönetmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Ayrıntılı bilgilerle yeni şirketler oluşturun</li>
                    <li>Mevcut şirket detaylarını düzenleyin</li>
                    <li>Şirket finansal özetlerini görüntüleyin</li>
                    <li>Şirket iletişim bilgilerini ve adreslerini yönetin</li>
                </ul>
                <h3>Yeni Şirket Oluşturma</h3>
                <ol>
                    <li>Kenar çubuğundaki Şirketler bölümüne gidin</li>
                    <li>"Yeni Şirket" düğmesine tıklayın</li>
                    <li>Gerekli bilgileri doldurun</li>
                    <li>Şirketi oluşturmak için "Kaydet"e tıklayın</li>
                </ol>',
            slug: 'managing-companies',
            category: 'Companies',
            order: 1
        );

        // Projects Category
        $this->createHelpDocument(
            title_en: 'Project Management',
            title_tr: 'Proje Yönetimi',
            content_en: '<h2>Managing Projects</h2>
                <p>The Projects section allows you to create and manage all your projects efficiently.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Create new projects with detailed specifications</li>
                    <li>Assign projects to specific companies</li>
                    <li>Track project progress and status</li>
                    <li>Manage project budgets and financial details</li>
                    <li>Add tasks to projects for detailed work tracking</li>
                </ul>
                <h3>Creating a New Project</h3>
                <ol>
                    <li>Navigate to the Projects section in the sidebar</li>
                    <li>Click the "New Project" button</li>
                    <li>Fill in the project details including name, company, and description</li>
                    <li>Set the project budget and timeline</li>
                    <li>Click "Save" to create the project</li>
                </ol>',
            content_tr: '<h2>Proje Yönetimi</h2>
                <p>Projeler bölümü, tüm projelerinizi verimli bir şekilde oluşturmanızı ve yönetmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Ayrıntılı özelliklerle yeni projeler oluşturun</li>
                    <li>Projeleri belirli şirketlere atayın</li>
                    <li>Proje ilerlemesini ve durumunu takip edin</li>
                    <li>Proje bütçelerini ve finansal detayları yönetin</li>
                    <li>Detaylı iş takibi için projelere görevler ekleyin</li>
                </ul>
                <h3>Yeni Proje Oluşturma</h3>
                <ol>
                    <li>Kenar çubuğundaki Projeler bölümüne gidin</li>
                    <li>"Yeni Proje" düğmesine tıklayın</li>
                    <li>Ad, şirket ve açıklama dahil olmak üzere proje detaylarını doldurun</li>
                    <li>Proje bütçesini ve zaman çizelgesini ayarlayın</li>
                    <li>Projeyi oluşturmak için "Kaydet"e tıklayın</li>
                </ol>',
            slug: 'project-management',
            category: 'Projects',
            order: 1
        );

        // Tasks Category
        $this->createHelpDocument(
            title_en: 'Task Management',
            title_tr: 'Görev Yönetimi',
            content_en: '<h2>Managing Tasks</h2>
                <p>The Tasks section allows you to create and track individual tasks within your projects.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Create tasks with detailed descriptions</li>
                    <li>Assign tasks to specific projects</li>
                    <li>Set task priorities and deadlines</li>
                    <li>Track task completion status</li>
                    <li>Manage task payments and financial details</li>
                </ul>
                <h3>Creating a New Task</h3>
                <ol>
                    <li>Navigate to the Tasks section in the sidebar</li>
                    <li>Click the "New Task" button</li>
                    <li>Select the associated project</li>
                    <li>Fill in the task details including name, description, and deadline</li>
                    <li>Set the task budget and payment terms</li>
                    <li>Click "Save" to create the task</li>
                </ol>
                <h3>Task Payment Actions</h3>
                <p>You can use the "Pay" and "Get Paid" actions to record payments for tasks.</p>',
            content_tr: '<h2>Görev Yönetimi</h2>
                <p>Görevler bölümü, projeleriniz içindeki bireysel görevleri oluşturmanızı ve takip etmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Ayrıntılı açıklamalarla görevler oluşturun</li>
                    <li>Görevleri belirli projelere atayın</li>
                    <li>Görev önceliklerini ve son tarihlerini belirleyin</li>
                    <li>Görev tamamlanma durumunu takip edin</li>
                    <li>Görev ödemelerini ve finansal detayları yönetin</li>
                </ul>
                <h3>Yeni Görev Oluşturma</h3>
                <ol>
                    <li>Kenar çubuğundaki Görevler bölümüne gidin</li>
                    <li>"Yeni Görev" düğmesine tıklayın</li>
                    <li>İlişkili projeyi seçin</li>
                    <li>Ad, açıklama ve son tarih dahil olmak üzere görev detaylarını doldurun</li>
                    <li>Görev bütçesini ve ödeme koşullarını belirleyin</li>
                    <li>Görevi oluşturmak için "Kaydet"e tıklayın</li>
                </ol>
                <h3>Görev Ödeme İşlemleri</h3>
                <p>Görevler için ödemeleri kaydetmek için "Öde" ve "Ödeme Al" eylemlerini kullanabilirsiniz.</p>',
            slug: 'task-management',
            category: 'Tasks',
            order: 1
        );

        // Accounts Category
        $this->createHelpDocument(
            title_en: 'Financial Accounts',
            title_tr: 'Finansal Hesaplar',
            content_en: '<h2>Managing Financial Accounts</h2>
                <p>The Accounts section allows you to manage your financial accounts and track transactions.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Create and manage different account types</li>
                    <li>Track account balances</li>
                    <li>Record transactions between accounts</li>
                    <li>Generate financial reports</li>
                </ul>
                <h3>Creating a New Account</h3>
                <ol>
                    <li>Navigate to the Accounts section in the sidebar</li>
                    <li>Click the "New Account" button</li>
                    <li>Select the account type and group</li>
                    <li>Enter the account name and initial balance</li>
                    <li>Click "Save" to create the account</li>
                </ol>',
            content_tr: '<h2>Finansal Hesapları Yönetme</h2>
                <p>Hesaplar bölümü, finansal hesaplarınızı yönetmenizi ve işlemleri takip etmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Farklı hesap türleri oluşturun ve yönetin</li>
                    <li>Hesap bakiyelerini takip edin</li>
                    <li>Hesaplar arası işlemleri kaydedin</li>
                    <li>Finansal raporlar oluşturun</li>
                </ul>
                <h3>Yeni Hesap Oluşturma</h3>
                <ol>
                    <li>Kenar çubuğundaki Hesaplar bölümüne gidin</li>
                    <li>"Yeni Hesap" düğmesine tıklayın</li>
                    <li>Hesap türünü ve grubunu seçin</li>
                    <li>Hesap adını ve başlangıç bakiyesini girin</li>
                    <li>Hesabı oluşturmak için "Kaydet"e tıklayın</li>
                </ol>',
            slug: 'financial-accounts',
            category: 'Accounts',
            order: 1
        );

        // Transactions Category
        $this->createHelpDocument(
            title_en: 'Managing Transactions',
            title_tr: 'İşlemleri Yönetme',
            content_en: '<h2>Financial Transactions</h2>
                <p>The Transactions section allows you to record and manage all financial transactions.</p>
                <h3>Key Features</h3>
                <ul>
                    <li>Record income and expense transactions</li>
                    <li>Transfer funds between accounts</li>
                    <li>Link transactions to projects and tasks</li>
                    <li>Generate transaction reports</li>
                </ul>
                <h3>Creating a New Transaction</h3>
                <ol>
                    <li>Navigate to the Transactions section in the sidebar</li>
                    <li>Click the "New Transaction" button</li>
                    <li>Select the transaction type (income, expense, transfer)</li>
                    <li>Select the accounts involved</li>
                    <li>Enter the amount and description</li>
                    <li>Link to a project or task if applicable</li>
                    <li>Click "Save" to record the transaction</li>
                </ol>',
            content_tr: '<h2>Finansal İşlemler</h2>
                <p>İşlemler bölümü, tüm finansal işlemleri kaydetmenizi ve yönetmenizi sağlar.</p>
                <h3>Temel Özellikler</h3>
                <ul>
                    <li>Gelir ve gider işlemlerini kaydedin</li>
                    <li>Hesaplar arası fon transferi yapın</li>
                    <li>İşlemleri projeler ve görevlerle ilişkilendirin</li>
                    <li>İşlem raporları oluşturun</li>
                </ul>
                <h3>Yeni İşlem Oluşturma</h3>
                <ol>
                    <li>Kenar çubuğundaki İşlemler bölümüne gidin</li>
                    <li>"Yeni İşlem" düğmesine tıklayın</li>
                    <li>İşlem türünü seçin (gelir, gider, transfer)</li>
                    <li>İlgili hesapları seçin</li>
                    <li>Tutarı ve açıklamayı girin</li>
                    <li>Varsa bir proje veya görevle ilişkilendirin</li>
                    <li>İşlemi kaydetmek için "Kaydet"e tıklayın</li>
                </ol>',
            slug: 'managing-transactions',
            category: 'Transactions',
            order: 1
        );
    }

    private function createHelpDocument(
        string $title_en,
        string $title_tr,
        string $content_en,
        string $content_tr,
        string $slug,
        string $category,
        int $order,
        ?int $parentId = null
    ): void {
        // Create or update English version
        HelpDocument::updateOrCreate(
            ['slug' => $slug, 'language_code' => 'en'],
            [
                'title' => $title_en,
                'content' => $content_en,
                'category' => $category,
                'order' => $order,
                'parent_id' => $parentId,
            ]
        );
        
        // Create or update Turkish version
        HelpDocument::updateOrCreate(
            ['slug' => $slug, 'language_code' => 'tr'],
            [
                'title' => $title_tr,
                'content' => $content_tr,
                'category' => $category,
                'order' => $order,
                'parent_id' => $parentId,
            ]
        );
    }
}
