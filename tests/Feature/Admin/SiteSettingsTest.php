<?php

namespace Tests\Feature\Admin;

use App\Filament\Pages\ManageSiteSettings;
use App\Support\SiteSettings;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class SiteSettingsTest extends AdminTestCase
{
    public function test_form_is_filled_from_site_settings(): void
    {
        app(SiteSettings::class)->set([
            'site_name' => ['ar' => 'قنوات الاستشارات', 'en' => 'Advisory Channels'],
            'hero_title' => ['ar' => 'إرث سعودي', 'en' => 'Saudi Heritage'],
            'contact_email' => 'info@advisory-channels.com',
            'home_stats' => [['value' => '2019', 'label' => ['ar' => 'سنة التأسيس', 'en' => 'Founded']]],
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ManageSiteSettings::class)
            ->assertSchemaStateSet([
                'hero_title.ar' => 'إرث سعودي',
                'hero_title.en' => 'Saudi Heritage',
                'contact_email' => 'info@advisory-channels.com',
            ]);
    }

    public function test_saving_updates_site_settings(): void
    {
        app(SiteSettings::class)->set([
            'site_name' => ['ar' => 'قنوات الاستشارات', 'en' => 'Advisory Channels'],
            'hero_title' => ['ar' => 'إرث سعودي', 'en' => 'Saudi Heritage'],
            'contact_email' => 'info@advisory-channels.com',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'hero_title.ar' => 'عنوان جديد',
                'contact_phone' => '+966 11 000 0000',
                'notification_email' => 'leads@advisory-channels.com',
                'home_stats' => [
                    ['value' => '+20', 'label' => ['ar' => 'عاماً من الخبرة', 'en' => 'Years of experience']],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified();

        $settings = app(SiteSettings::class);
        $settings->flush();

        $this->assertSame('عنوان جديد', $settings->tr('hero_title', 'ar'));
        $this->assertSame('Saudi Heritage', $settings->tr('hero_title', 'en'));
        $this->assertSame('+966 11 000 0000', $settings->get('contact_phone'));
        $this->assertSame('leads@advisory-channels.com', $settings->get('notification_email'));
        $this->assertEquals(
            [['value' => '+20', 'icon' => null, 'label' => ['ar' => 'عاماً من الخبرة', 'en' => 'Years of experience']]],
            $settings->get('home_stats'),
        );
    }

    public function test_contact_email_is_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'site_name' => ['ar' => 'قنوات', 'en' => 'Channels'],
                'hero_title' => ['ar' => 'عنوان', 'en' => 'Title'],
                'contact_email' => null,
            ])
            ->call('save')
            ->assertHasFormErrors(['contact_email' => 'required']);
    }

    public function test_saving_stores_cinematic_images_and_stat_icons(): void
    {
        Storage::fake('public');

        app(SiteSettings::class)->set([
            'site_name' => ['ar' => 'قنوات الاستشارات', 'en' => 'Advisory Channels'],
            'hero_title' => ['ar' => 'إرث سعودي', 'en' => 'Saudi Heritage'],
            'contact_email' => 'info@advisory-channels.com',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'hero_image' => UploadedFile::fake()->image('hero.jpg', 1920, 1080),
                'about_image' => UploadedFile::fake()->image('about.jpg', 1600, 900),
                'home_stats' => [
                    ['value' => '2019', 'icon' => 'building', 'label' => ['ar' => 'سنة التأسيس', 'en' => 'Founded']],
                    ['value' => '7', 'icon' => 'rocket', 'label' => ['ar' => 'مراحل', 'en' => 'Stages']],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings = app(SiteSettings::class);
        $settings->flush();

        $heroImage = $settings->get('hero_image');

        $this->assertIsString($heroImage);
        $this->assertStringStartsWith('branding/', $heroImage);
        Storage::disk('public')->assertExists($heroImage);
        Storage::disk('public')->assertExists($settings->get('about_image'));
        $this->assertSame(['building', 'rocket'], array_column($settings->get('home_stats'), 'icon'));
    }

    public function test_stat_icon_must_be_a_known_icon(): void
    {
        app(SiteSettings::class)->set([
            'site_name' => ['ar' => 'قنوات الاستشارات', 'en' => 'Advisory Channels'],
            'hero_title' => ['ar' => 'إرث سعودي', 'en' => 'Saudi Heritage'],
            'contact_email' => 'info@advisory-channels.com',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(ManageSiteSettings::class)
            ->fillForm([
                'home_stats' => [
                    ['value' => '1', 'icon' => 'not-an-icon', 'label' => ['ar' => 'س', 'en' => 'S']],
                ],
            ])
            ->call('save')
            ->assertHasFormErrors();
    }
}
