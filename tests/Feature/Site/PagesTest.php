<?php

namespace Tests\Feature\Site;

use App\Models\Service;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed inside the test transaction: the in-memory database is migrated once and shared by every test.
        $this->seed(DatabaseSeeder::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pages(): array
    {
        return collect([
            '', '/about', '/services', '/services/strategy-growth', '/saudi-gate', '/careers',
            '/careers/path-to-consultancy', '/careers/apply', '/faqs', '/request-service',
            '/request-service?service=grow', '/contact', '/privacy-policy', '/terms-and-conditions',
        ])->mapWithKeys(fn (string $path) => [$path ?: '/' => [$path]])->all();
    }

    #[Test]
    #[DataProvider('pages')]
    public function every_page_renders_in_arabic_and_english(string $path): void
    {
        $this->get("/ar{$path}")
            ->assertOk()
            ->assertSee('<html lang="ar" dir="rtl"', false)
            ->assertSee('hreflang="en"', false);

        $this->get("/en{$path}")
            ->assertOk()
            ->assertSee('<html lang="en" dir="ltr"', false);
    }

    #[Test]
    public function the_bare_domain_opens_the_arabic_site(): void
    {
        $this->get('/')->assertRedirect('/ar');
    }

    #[Test]
    public function unsupported_locales_and_unknown_records_are_not_found(): void
    {
        $this->get('/fr/about')->assertNotFound();
        $this->get('/ar/services/unknown')->assertNotFound();
        $this->get('/ar/careers/unknown')->assertNotFound();
    }

    #[Test]
    public function hidden_services_are_not_found(): void
    {
        Service::query()->where('slug', 'strategy-growth')->update(['is_active' => false]);

        $this->get('/ar/services/strategy-growth')->assertNotFound();
        $this->get('/ar/services')->assertDontSee('/ar/services/strategy-growth');
    }

    #[Test]
    public function content_is_shown_in_the_page_language(): void
    {
        $this->get('/ar/services')->assertSee('قناة الاستراتيجية والنمو');
        $this->get('/en/services')->assertSee('Strategy &amp; Growth Channel', false);
    }

    #[Test]
    public function the_language_switcher_keeps_the_current_page(): void
    {
        $this->get('/ar/services/strategy-growth')
            ->assertSee('href="'.url('/en/services/strategy-growth').'"', false);
    }

    #[Test]
    public function x_default_always_points_to_the_arabic_page(): void
    {
        $this->get('/en/about')
            ->assertSee('<link rel="alternate" hreflang="x-default" href="'.url('/ar/about').'">', false);
    }

    #[Test]
    public function old_wix_urls_redirect_permanently(): void
    {
        $this->get('/about-3')->assertStatus(301)->assertRedirect('/en/about');
        $this->get('/services-6?lang=ar')->assertStatus(301)->assertRedirect('/ar/services');
        $this->get('/general-5')->assertRedirect('/en/careers');
        $this->get('/general-8')->assertRedirect('/en/privacy-policy');
        $this->get('/saudi-gate')->assertRedirect('/en/saudi-gate');
    }

    #[Test]
    public function the_sitemap_lists_both_languages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(url('/ar/services/strategy-growth'))
            ->assertSee(url('/en/careers/path-to-consultancy'));
    }
}
