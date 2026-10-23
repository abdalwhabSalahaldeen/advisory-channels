<?php

namespace Tests\Feature\Site;

use App\Support\SiteSettings;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrustContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
    }

    #[Test]
    public function the_seeded_examples_stay_off_the_site(): void
    {
        $this->get('/en/about')
            ->assertOk()
            ->assertDontSee('(example)')
            ->assertDontSee(__('about.credentials_title', [], 'en'));

        $this->get('/en')
            ->assertOk()
            ->assertDontSee('Example Partner One');
    }

    #[Test]
    public function visible_credentials_show_in_the_footer_and_about_page(): void
    {
        app(SiteSettings::class)->set(['credentials' => [
            ['name' => ['ar' => 'السجل التجاري', 'en' => 'Commercial Registration'], 'number' => '1010123456', 'visible' => true],
            ['name' => ['ar' => 'شهادة مخفية', 'en' => 'Hidden certificate'], 'number' => null, 'visible' => false],
        ]]);

        $this->get('/en/about')
            ->assertOk()
            ->assertSee(__('about.credentials_title', [], 'en'))
            ->assertSee('Commercial Registration')
            ->assertSee('1010123456')
            ->assertDontSee('Hidden certificate');

        $this->get('/ar')
            ->assertOk()
            ->assertSee(__('footer.credentials', [], 'ar'))
            ->assertSee('السجل التجاري');
    }
}
