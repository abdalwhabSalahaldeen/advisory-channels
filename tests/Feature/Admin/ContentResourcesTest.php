<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Docs\Pages\EditDoc;
use App\Filament\Resources\Faqs\Pages\CreateFaq;
use App\Filament\Resources\Subscribers\Pages\ManageSubscribers;
use App\Models\Faq;
use App\Models\FaqCategory;
use Livewire\Livewire;

class ContentResourcesTest extends AdminTestCase
{
    public function test_doc_translations_can_be_edited_but_not_deleted(): void
    {
        $doc = $this->createDoc();

        $this->actingAs($this->admin);

        Livewire::test(EditDoc::class, ['record' => $doc->getKey()])
            ->assertActionDoesNotExist('delete')
            ->fillForm([
                'title.ar' => 'سياسة الخصوصية وحماية البيانات',
                'body.en' => '<p>Updated policy.</p>',
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $doc->refresh();

        $this->assertSame('سياسة الخصوصية وحماية البيانات', $doc->tr('title', 'ar'));
        $this->assertStringContainsString('Updated policy.', $doc->tr('body', 'en'));
        $this->assertSame('privacy-policy', $doc->slug);
    }

    public function test_faq_is_created_in_a_category(): void
    {
        $category = FaqCategory::query()->create(['slug' => 'saudi-gate', 'name' => ['ar' => 'البوابة السعودية', 'en' => 'Saudi Gate']]);

        $this->actingAs($this->admin);

        Livewire::test(CreateFaq::class)
            ->fillForm([
                'faq_category_id' => $category->id,
                'question.ar' => 'كم تستغرق إجراءات التأسيس؟',
                'question.en' => 'How long does company setup take?',
                'answer.ar' => 'تختلف حسب النشاط.',
                'answer.en' => 'It depends on the activity.',
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $faq = Faq::query()->sole();

        $this->assertTrue($faq->category->is($category));
        $this->assertSame('How long does company setup take?', $faq->tr('question', 'en'));
        $this->assertTrue($faq->is_active);
    }

    public function test_subscribers_can_be_exported_as_csv(): void
    {
        $this->createSubscriber(['email' => 'first@example.com']);
        $this->createSubscriber(['email' => 'second@example.com', 'locale' => 'en']);

        $this->actingAs($this->admin);

        Livewire::test(ManageSubscribers::class)
            ->callAction('exportCsv')
            ->assertFileDownloaded('subscribers-'.now()->format('Y-m-d').'.csv');
    }
}
