<?php

namespace Tests\Feature\Site;

use App\Livewire\SubscribeForm;
use App\Support\SiteSettings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ContactAndSeoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    #[Test]
    public function local_whatsapp_numbers_become_international(): void
    {
        $settings = app(SiteSettings::class);

        foreach (['055 123 4567' => '966551234567', '+966 55 123 4567' => '966551234567', '00966551234567' => '966551234567', '551234567' => '966551234567'] as $input => $expected) {
            $settings->set(['whatsapp' => $input]);

            $this->assertSame($expected, $settings->whatsappNumber(), $input);
        }

        $settings->set(['whatsapp' => null]);
        $this->assertNull($settings->whatsappUrl());
    }

    #[Test]
    public function the_contact_page_shows_whatsapp_national_address_and_map(): void
    {
        app(SiteSettings::class)->set([
            'contact_phone' => '011 222 7777',
            'whatsapp' => '0551234567',
            'national_address' => [
                'building_number' => '8228',
                'street' => ['ar' => 'طريق الملك فهد', 'en' => 'King Fahd Road'],
                'district' => ['ar' => 'العليا', 'en' => 'Al Olaya'],
                'city' => ['ar' => 'الرياض', 'en' => 'Riyadh'],
                'postal_code' => '12214',
                'additional_number' => '2121',
                'short_address' => 'RAAA8228',
            ],
            'map_location' => '24.7136, 46.6753',
        ]);

        $this->get('/en/contact')
            ->assertOk()
            ->assertSee('https://wa.me/966551234567', false)
            ->assertSee('tel:0112227777', false)
            ->assertSee('8228 King Fahd Road')
            ->assertSee('Al Olaya, Riyadh 12214')
            ->assertSee('Short address RAAA8228')
            ->assertSee('https://maps.google.com/maps?q=24.7136%2C%2046.6753', false);

        $this->get('/ar/contact')
            ->assertOk()
            ->assertSee('8228 طريق الملك فهد')
            ->assertSee('العليا، الرياض 12214');
    }

    #[Test]
    public function the_map_and_national_address_stay_hidden_until_set(): void
    {
        $this->get('/en/contact')
            ->assertOk()
            ->assertDontSee('maps.google.com/maps', false)
            ->assertDontSee(__('contact.national_address', [], 'en'));
    }

    #[Test]
    public function the_organization_schema_carries_the_address_and_coordinates(): void
    {
        app(SiteSettings::class)->set([
            'contact_phone' => '011 222 7777',
            'national_address' => ['building_number' => '8228', 'street' => ['en' => 'King Fahd Road'], 'city' => ['en' => 'Riyadh'], 'postal_code' => '12214'],
            'map_location' => '24.7136, 46.6753',
            'google_business_url' => 'https://maps.app.goo.gl/example',
        ]);

        $schema = app(SiteSettings::class)->organizationSchema();

        $this->assertSame('ProfessionalService', $schema['@type']);
        $this->assertSame('011 222 7777', $schema['telephone']);
        $this->assertSame('8228 King Fahd Road', $schema['address']['streetAddress']);
        $this->assertSame(24.7136, $schema['geo']['latitude']);
        $this->assertContains('https://maps.app.goo.gl/example', $schema['sameAs']);
    }

    #[Test]
    public function services_and_faqs_publish_structured_data(): void
    {
        $this->get('/en/services/strategy-growth')
            ->assertOk()
            ->assertSee('"@type":"Service"', false)
            ->assertSee('"@type":"BreadcrumbList"', false);

        $this->get('/en/faqs')
            ->assertOk()
            ->assertSee('"@type":"FAQPage"', false)
            ->assertSee('"@type":"Question"', false);
    }

    #[Test]
    public function the_sitemap_lists_both_languages(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee(url('/ar/services/strategy-growth'), false)
            ->assertSee(url('/en/services/strategy-growth'), false);
    }

    #[Test]
    public function analytics_loads_only_with_a_measurement_id(): void
    {
        $this->get('/en')->assertOk()->assertDontSee('googletagmanager.com', false);

        app(SiteSettings::class)->set(['ga_measurement_id' => 'G-TEST123', 'google_site_verification' => 'verify-me']);

        $this->get('/en')
            ->assertOk()
            ->assertSee('googletagmanager.com/gtag/js?id=G-TEST123', false)
            ->assertSee('<meta name="google-site-verification" content="verify-me">', false);
    }

    #[Test]
    public function forms_dispatch_a_tracking_event_on_success(): void
    {
        Livewire::test(SubscribeForm::class)
            ->set('email', 'reader@example.com')
            ->call('submit')
            ->assertDispatched('track', name: 'sign_up');
    }
}
