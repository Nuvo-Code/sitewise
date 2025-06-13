<?php

return [
    'resources' => [
        'site' => [
            'navigation_label' => 'Site Ayarları',
            'model_label' => 'Site Ayarları',
            'plural_model_label' => 'Site Ayarları',
            'tabs' => [
                'site_settings' => 'Site Ayarları',
                'general' => 'Genel',
                'seo_analytics' => 'SEO ve Analitik',
                'social_contact' => 'Sosyal ve İletişim',
                'appearance' => 'Görünüm',
                'advanced' => 'Gelişmiş',
                'ai_configuration' => 'AI Yapılandırması',
            ],
            'fields' => [
                'domain' => [
                    'label' => 'Domain',
                    'helper' => 'Bu domain otomatik olarak algılanır ve değiştirilemez',
                ],
                'name' => [
                    'label' => 'Site Adı',
                    'helper' => 'Admin panelinde görünen sitenizin dostça adı',
                ],
                'description' => [
                    'label' => 'Site Açıklaması',
                    'helper' => 'Sitenizin kısa açıklaması (SEO ve sosyal paylaşım için kullanılır)',
                ],
                'tagline' => [
                    'label' => 'Slogan',
                    'helper' => 'Sitenizi tanımlayan kısa, akılda kalıcı ifade',
                ],
                'active' => [
                    'label' => 'Site Aktif',
                    'helper' => 'Siteyi geçici olarak çevrimdışı almak için devre dışı bırakın',
                ],
                'meta_title' => [
                    'label' => 'Meta Başlık',
                    'helper' => 'Arama sonuçlarında görünen başlık (maksimum 60 karakter)',
                ],
                'meta_keywords' => [
                    'label' => 'Meta Anahtar Kelimeler',
                    'helper' => 'Site içeriğinizle ilgili anahtar kelimeler',
                ],
                'meta_description' => [
                    'label' => 'Meta Açıklama',
                    'helper' => 'Arama sonuçlarında görünen açıklama (maksimum 160 karakter)',
                ],
                'google_analytics_id' => [
                    'label' => 'Google Analytics ID',
                    'helper' => 'Google Analytics ölçüm ID\'niz',
                ],
                'google_search_console' => [
                    'label' => 'Google Search Console Doğrulama',
                    'helper' => 'Google Search Console doğrulama meta etiketi içeriği',
                ],
                'custom_head_code' => [
                    'label' => 'Özel Head Kodu',
                    'helper' => '<head> bölümüne eklenecek özel HTML kodu',
                ],
                'contact_email' => [
                    'label' => 'İletişim E-postası',
                    'helper' => 'Sitenizin birincil iletişim e-postası',
                ],
                'contact_phone' => [
                    'label' => 'İletişim Telefonu',
                    'helper' => 'Birincil iletişim telefon numarası',
                ],
                'contact_address' => [
                    'label' => 'İletişim Adresi',
                    'helper' => 'Fiziksel adres veya posta adresi',
                ],
                'social_links' => [
                    'label' => 'Sosyal Bağlantılar',
                    'helper' => 'Sosyal medya profillerinize bağlantılar ekleyin',
                    'add_action' => 'Sosyal Bağlantı Ekle',
                    'platform' => 'Platform',
                    'url' => 'URL',
                ],
                'primary_color' => [
                    'label' => 'Ana Renk',
                    'helper' => 'Sitenizin ana marka rengi',
                ],
                'secondary_color' => [
                    'label' => 'İkincil Renk',
                    'helper' => 'İkincil marka rengi',
                ],
                'theme' => [
                    'label' => 'Tema',
                    'helper' => 'Sitenizin varsayılan teması',
                    'options' => [
                        'light' => 'Açık',
                        'dark' => 'Koyu',
                        'auto' => 'Otomatik (Sistem Tercihi)',
                    ],
                ],
                'logo_url' => [
                    'label' => 'Logo',
                    'helper' => 'Site logo resminizi yükleyin (PNG, JPG, SVG, maksimum 2MB)',
                ],
                'favicon_url' => [
                    'label' => 'Favicon',
                    'helper' => 'Site favicon\'inizi yükleyin (.ico veya .png, maksimum 512KB)',
                ],
                'maintenance_mode' => [
                    'label' => 'Bakım Modu',
                    'helper' => 'Ziyaretçilere bakım sayfası göstermek için etkinleştirin',
                ],
                'maintenance_message' => [
                    'label' => 'Bakım Mesajı',
                    'helper' => 'Bakım sırasında ziyaretçilere gösterilecek mesaj',
                    'default' => 'Şu anda planlanmış bakım gerçekleştiriyoruz. Lütfen kısa süre sonra tekrar kontrol edin!',
                ],
                'timezone' => [
                    'label' => 'Saat Dilimi',
                    'helper' => 'Sitenizin varsayılan saat dilimi',
                ],
                'language' => [
                    'label' => 'Varsayılan Dil',
                    'helper' => 'Site içeriğinizin varsayılan dili',
                ],
                'custom_css' => [
                    'label' => 'Özel CSS',
                    'helper' => 'Sitenize uygulanacak özel CSS stilleri',
                ],
                'custom_js' => [
                    'label' => 'Özel JavaScript',
                    'helper' => 'Sitenize dahil edilecek özel JavaScript kodu',
                ],
                'ai_enabled' => [
                    'label' => 'AI İçerik Üretimini Etkinleştir',
                    'helper' => 'İçerik üretimi için AI yüzen butonunu etkinleştirin',
                ],
                'ai_provider' => [
                    'label' => 'AI Sağlayıcısı',
                    'helper' => 'Tercih ettiğiniz AI sağlayıcısını seçin',
                ],
                'ai_api_key' => [
                    'label' => 'API Anahtarı',
                    'helper' => 'Seçilen sağlayıcı için API anahtarınız (güvenli şekilde saklanır)',
                    'placeholder' => 'API anahtarınızı girin...',
                ],
                'ai_model' => [
                    'label' => 'AI Modeli',
                    'helper' => 'İçerik üretimi için kullanılacak AI modelini seçin',
                ],
            ],
            'actions' => [
                'test_configuration' => 'Yapılandırmayı Test Et',
            ],
            'notifications' => [
                'test_success_title' => 'Yapılandırma Testi Başarılı',
                'test_success_body' => 'AI yapılandırması doğru şekilde çalışıyor!',
                'test_failed_title' => 'Yapılandırma Testi Başarısız',
            ],
        ],
        'page' => [
            'navigation_label' => 'Sayfalar',
            'model_label' => 'Sayfa',
            'plural_model_label' => 'Sayfalar',
            'tabs' => [
                'page_settings' => 'Sayfa Ayarları',
                'basic_info' => 'Temel Bilgiler',
                'content' => 'İçerik',
                'seo_settings' => 'SEO Ayarları',
            ],
            'fields' => [
                'slug' => [
                    'label' => 'URL Slug',
                    'helper' => 'Sadece küçük harfler, sayılar ve tireler kullanılabilir',
                ],
                'title' => [
                    'label' => 'Sayfa Başlığı',
                ],
                'response_type' => [
                    'label' => 'Yanıt Türü',
                    'helper' => 'Bu sayfanın nasıl render edileceği',
                    'options' => [
                        'html' => 'HTML',
                        'markdown' => 'Markdown',
                        'json' => 'JSON',
                        'template' => 'Şablon',
                    ],
                ],
                'template_id' => [
                    'label' => 'Şablon',
                    'helper' => 'Bu sayfa için bir şablon seçin',
                ],
                'content' => [
                    'label' => 'İçerik',
                    'helper' => 'Sayfa içeriği (HTML, Markdown veya JSON)',
                ],
                'active' => [
                    'label' => 'Aktif',
                    'helper' => 'Bu sayfanın herkese açık olup olmadığı',
                ],
                'meta_title' => [
                    'label' => 'Meta Başlık',
                    'helper' => 'Bu sayfa için SEO başlığı',
                ],
                'meta_description' => [
                    'label' => 'Meta Açıklama',
                    'helper' => 'Bu sayfa için SEO açıklaması',
                ],
                'meta_keywords' => [
                    'label' => 'Meta Anahtar Kelimeler',
                    'helper' => 'Bu sayfa için SEO anahtar kelimeleri',
                ],
            ],
            'table' => [
                'columns' => [
                    'slug' => 'Slug',
                    'title' => 'Başlık',
                    'type' => 'Tür',
                    'template' => 'Şablon',
                    'active' => 'Aktif',
                    'last_updated' => 'Son Güncelleme',
                ],
            ],
            'actions' => [
                'visit_page' => 'Sayfayı Ziyaret Et',
            ],
        ],
        'template' => [
            'navigation_label' => 'Şablonlar',
            'model_label' => 'Şablon',
            'plural_model_label' => 'Şablonlar',
            'tabs' => [
                'template_settings' => 'Şablon Ayarları',
                'basic_info' => 'Temel Bilgiler',
                'template_content' => 'Şablon İçeriği',
                'fields_configuration' => 'Alan Yapılandırması',
                'asset_paths' => 'Varlık Yolları',
            ],
            'fields' => [
                'name' => [
                    'label' => 'Şablon Adı',
                    'helper' => 'Bu şablon için açıklayıcı bir ad',
                ],
                'description' => [
                    'label' => 'Açıklama',
                    'helper' => 'Bu şablonun ne için olduğunun kısa açıklaması',
                ],
                'content' => [
                    'label' => 'Şablon İçeriği',
                    'helper' => 'Yer tutucularla Blade şablon içeriği',
                ],
                'fields' => [
                    'label' => 'Şablon Alanları',
                    'helper' => 'Bu şablonu kullanırken doldurulabilecek alanları tanımlayın',
                    'add_action' => 'Alan Ekle',
                    'key' => 'Alan Anahtarı',
                    'name' => 'Alan Adı',
                    'type' => 'Alan Türü',
                    'required' => 'Gerekli',
                    'default_value' => 'Varsayılan Değer',
                ],
                'active' => [
                    'label' => 'Aktif',
                    'helper' => 'Bu şablonun kullanım için mevcut olup olmadığı',
                ],
            ],
            'table' => [
                'columns' => [
                    'name' => 'Ad',
                    'description' => 'Açıklama',
                    'pages_using' => 'Kullanan Sayfalar',
                    'active' => 'Aktif',
                    'created' => 'Oluşturuldu',
                ],
            ],
        ],
    ],
    'pages' => [
        'dashboard' => [
            'title' => 'Kontrol Paneli',
        ],
        'cache_management' => [
            'navigation_label' => 'Önbellek Yönetimi',
            'title' => 'Önbellek Yönetimi',
            'navigation_group' => 'Sistem',
        ],
        'ai_content_generation' => [
            'navigation_label' => 'AI İçerik',
            'title' => 'AI İçerik Üretimi',
            'navigation_group' => 'İçerik',
        ],
        'site_installation' => [
            'title' => 'Site Kurulumu',
            'heading' => 'Site Kurulumunu Tamamla',
            'subheading' => 'Başlamak için site ayarlarınızı yapılandırın',
            'complete_setup' => 'Kurulumu Tamamla',
        ],
    ],
    'widgets' => [
        'site_overview' => [
            'heading' => 'Site Genel Bakış',
            'current_site' => 'Mevcut Site',
            'total_pages' => 'Toplam Sayfa',
            'active_pages' => 'aktif',
            'templates' => 'Şablonlar',
            'reusable_layouts' => 'Yeniden kullanılabilir düzenler',
            'page_types' => 'Sayfa Türleri',
        ],
        'cache_performance' => [
            'site_cache_keys' => 'Site Önbellek Anahtarları',
            'cached_items' => 'önbelleğe alınmış öğeler',
            'simulated_data' => 'Simüle edilmiş veri (önbellek kullanılamıyor)',
            'pages_cached' => 'Önbelleğe Alınan Sayfalar',
            'cached_page_content' => 'Önbelleğe alınan sayfa içeriği',
            'simulated_count' => 'Simüle edilmiş sayım',
        ],
    ],
    'cache' => [
        'actions' => [
            'clear_site_cache' => 'Site Önbelleğini Temizle',
            'warm_site_cache' => 'Site Önbelleğini Isıt',
            'clear_pages_cache' => 'Sayfa Önbelleğini Temizle',
            'clear_templates_cache' => 'Şablon Önbelleğini Temizle',
            'debug_cache' => 'Önbellek Hata Ayıklama',
        ],
        'modals' => [
            'clear_site_cache_heading' => 'Site Önbelleğini Temizle',
            'clear_site_cache_description' => 'Bu, :site için tüm önbelleğe alınmış verileri temizleyecek. Emin misiniz?',
            'clear_pages_cache_heading' => 'Sayfa Önbelleğini Temizle',
            'clear_pages_cache_description' => 'Bu, :site için tüm sayfa önbelleğini temizleyecek.',
            'clear_templates_cache_heading' => 'Şablon Önbelleğini Temizle',
            'clear_templates_cache_description' => 'Bu, :site için tüm şablon önbelleğini temizleyecek.',
        ],
        'notifications' => [
            'site_cache_cleared' => 'Site önbelleği başarıyla temizlendi',
            'pages_cache_cleared' => 'Sayfa önbelleği başarıyla temizlendi',
            'templates_cache_cleared' => 'Şablon önbelleği başarıyla temizlendi',
            'cache_warmed_title' => 'Site önbelleği başarıyla ısıtıldı',
            'cache_warmed_body' => 'Önbellek ısıtıldı: :pages sayfa, :templates şablon',
            'debug_complete' => 'Önbellek Hata Ayıklama Tamamlandı',
        ],
        'status' => [
            'cache_system_not_available' => 'Önbellek Sistemi Kullanılamıyor',
            'cache_unavailable_description' => 'Önbellek sistemi şu anda çalışmıyor (muhtemelen veritabanı bağlantı sorunları nedeniyle). Aşağıda gösterilen sayılar tanıtım amaçlı simüle edilmiştir. Veritabanı bağlantısı geri yüklendiğinde önbellek işlevselliği çalışacaktır.',
            'domain' => 'Domain',
            'cache_status' => 'Önbellek Durumu',
            'working' => 'Çalışıyor',
            'unavailable' => 'Kullanılamıyor',
            'site_cache_keys' => 'Site Önbellek Anahtarları',
            'cache_driver' => 'Önbellek Sürücüsü',
            'simulated' => 'Simüle Edilmiş',
        ],
        'breakdown' => [
            'heading' => 'Türe Göre Önbellek Dağılımı',
            'simulated_data' => '(Simüle Edilmiş Veri)',
            'site_data' => 'Site Verisi',
            'pages' => 'Sayfalar',
            'templates' => 'Şablonlar',
            'template_content' => 'Şablon İçeriği',
            'compiled_templates' => 'Derlenmiş Şablonlar',
            'statistics' => 'İstatistikler',
        ],
        'management' => [
            'heading' => 'Site Önbellek Yönetimi',
            'tips' => [
                'auto_clear' => 'Değişiklikleri kaydettiğinizde önbellek otomatik olarak temizlenir',
                'bulk_changes' => 'Toplu içerik değişiklikleri yaparken site önbelleğini temizleyin',
                'warm_after_clear' => 'Sayfa yükleme sürelerini iyileştirmek için temizledikten sonra önbelleği ısıtın',
                'clear_pages' => 'Birden fazla sayfayı güncellerken sayfa önbelleğini temizleyin',
                'clear_templates' => 'Şablon yapısını değiştirirken şablon önbelleğini temizleyin',
                'use_actions' => 'Bu sitenin önbelleğini yönetmek için yukarıdaki eylem düğmelerini kullanın',
            ],
        ],
        'commands' => [
            'heading' => ':site için Komut Satırı Yönetimi',
            'clear_site' => '# Bu site için önbelleği temizle',
            'warm_site' => '# Bu site için önbelleği ısıt',
            'clear_pages_only' => '# Bu site için sadece sayfa önbelleğini temizle',
            'clear_templates_only' => '# Bu site için sadece şablon önbelleğini temizle',
            'show_stats' => '# Önbellek istatistiklerini göster',
        ],
        'no_site' => [
            'heading' => 'Site Mevcut Değil',
            'description' => 'Önbellek yönetimi sadece kayıtlı bir domain\'den erişildiğinde kullanılabilir.',
        ],
    ],
    'ai' => [
        'generate_content' => [
            'heading' => 'AI İçerik Üret',
            'content_description' => 'İçerik Açıklaması',
            'placeholder' => 'Örnek: Mavi gradyan arka planlı modern tasarımla, eylem çağrısı butonlu bir teknoloji girişimi için hero bölümü oluştur...',
            'helper' => 'Üretmek istediğiniz içeriği detaylı olarak açıklayın. Stil, düzen ve işlevsellik konusunda spesifik olun.',
            'generating' => 'Üretiliyor...',
            'generate_content' => 'İçerik Üret',
            'new_generation' => 'Yeni Üretim',
        ],
        'results' => [
            'generated_content' => 'Üretilen İçerik',
            'preview' => 'Önizleme',
            'html_code' => 'HTML Kodu',
            'copy_html' => 'HTML\'yi Kopyala',
            'regenerate' => 'Yeniden Üret',
            'regenerating' => 'Yeniden Üretiliyor...',
        ],
    ],
];
