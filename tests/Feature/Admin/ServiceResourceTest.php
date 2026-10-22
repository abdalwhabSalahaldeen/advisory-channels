<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Services\Pages\CreateService;
use App\Filament\Resources\Services\Pages\EditService;
use App\Models\Service;
use Livewire\Livewire;

class ServiceResourceTest extends AdminTestCase
{
    public function test_english_title_fills_the_slug_on_create(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateService::class)
            ->fillForm(['title.en' => 'Digital Advisory'])
            ->assertSchemaStateSet(['slug' => 'digital-advisory']);
    }

    public function test_service_is_created_with_both_translations(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateService::class)
            ->fillForm([
                'title.ar' => 'الاستشارات الرقمية',
                'title.en' => 'Digital Advisory',
                'excerpt.ar' => 'نبذة عربية',
                'excerpt.en' => 'English excerpt',
                'body.en' => '<p>Body</p>',
                'icon' => 'chart',
                'highlights' => [
                    ['ar' => 'التحول الرقمي', 'en' => 'Digital transformation'],
                    ['ar' => 'الأمن السيبراني', 'en' => 'Cybersecurity'],
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $service = Service::query()->where('slug', 'digital-advisory')->sole();

        $this->assertSame(['ar' => 'الاستشارات الرقمية', 'en' => 'Digital Advisory'], $service->title);
        $this->assertSame('الاستشارات الرقمية', $service->tr('title', 'ar'));
        $this->assertSame('English excerpt', $service->tr('excerpt', 'en'));
        $this->assertSame(['التحول الرقمي', 'الأمن السيبراني'], $service->trList('highlights', 'ar'));
        $this->assertTrue(array_is_list($service->highlights), 'Repeater items should be stored as a JSON list.');
        $this->assertTrue($service->is_active);
    }

    public function test_both_titles_are_required(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(CreateService::class)
            ->fillForm(['title.ar' => 'عنوان', 'slug' => 'some-service'])
            ->call('create')
            ->assertHasFormErrors(['title.en' => 'required']);
    }

    public function test_service_is_filled_and_updated_through_the_edit_page(): void
    {
        $this->actingAs($this->admin);
        $service = $this->createService();

        Livewire::test(EditService::class, ['record' => $service->getRouteKey()])
            ->assertSchemaStateSet([
                'title.ar' => 'قناة الاستراتيجية والنمو',
                'title.en' => 'Strategy & Growth Channel',
            ])
            ->fillForm([
                'title.ar' => 'الاستراتيجية والنمو',
                'meta_title.en' => 'Strategy consulting in Saudi Arabia',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $service->refresh();

        $this->assertSame('الاستراتيجية والنمو', $service->tr('title', 'ar'));
        $this->assertSame('Strategy & Growth Channel', $service->tr('title', 'en'));
        $this->assertSame('Strategy consulting in Saudi Arabia', $service->tr('meta_title', 'en'));
        $this->assertSame(['دراسات السوق'], $service->trList('highlights', 'ar'));
        $this->assertSame('strategy-growth', $service->slug, 'Editing the English title must not change the slug.');
    }

    public function test_slug_must_be_unique(): void
    {
        $this->actingAs($this->admin);
        $this->createService();

        Livewire::test(CreateService::class)
            ->fillForm([
                'title.ar' => 'خدمة',
                'title.en' => 'Another',
                'slug' => 'strategy-growth',
            ])
            ->call('create')
            ->assertHasFormErrors(['slug' => 'unique']);
    }
}
