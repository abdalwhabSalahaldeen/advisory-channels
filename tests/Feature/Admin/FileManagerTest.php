<?php

namespace Tests\Feature\Admin;

use App\Exceptions\FileManagerException;
use App\Filament\Pages\ManageFiles;
use App\Models\User;
use App\Support\FileManager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\TestWith;

class FileManagerTest extends AdminTestCase
{
    private string $publicRoot;

    private string $privateRoot;

    private string $outside;

    protected function setUp(): void
    {
        parent::setUp();

        $base = storage_path('framework/testing/file-manager');
        File::deleteDirectory($base);

        $this->publicRoot = "{$base}/public";
        $this->privateRoot = "{$base}/private";
        $this->outside = "{$base}/outside";

        foreach ([$this->publicRoot, $this->privateRoot, $this->outside] as $directory) {
            File::ensureDirectoryExists($directory);
        }

        config([
            'site.file_manager.roots' => ['public' => $this->publicRoot, 'private' => $this->privateRoot],
            'logging.channels.files.path' => "{$base}/files.log",
        ]);

        $this->actingAs($this->admin);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory(storage_path('framework/testing/file-manager'));

        parent::tearDown();
    }

    public function test_lists_folders_before_files_and_hides_system_entries(): void
    {
        File::put("{$this->publicRoot}/b-report.pdf", 'pdf');
        File::put("{$this->publicRoot}/.gitignore", '*');
        File::ensureDirectoryExists("{$this->publicRoot}/z-folder");
        File::ensureDirectoryExists("{$this->publicRoot}/livewire-tmp");

        Livewire::test(ManageFiles::class)
            ->assertCanSeeTableRecords(['z-folder', 'b-report.pdf'], inOrder: true)
            ->assertCanNotSeeTableRecords(['.gitignore', 'livewire-tmp']);
    }

    public function test_shows_octal_and_symbolic_permissions(): void
    {
        File::put("{$this->publicRoot}/logo.svg", '<svg/>');
        chmod("{$this->publicRoot}/logo.svg", 0640);

        Livewire::test(ManageFiles::class)->assertSee('0640')->assertSee('-rw-r-----');
    }

    public function test_opens_a_sub_folder_from_the_url(): void
    {
        File::ensureDirectoryExists("{$this->privateRoot}/cvs");
        File::put("{$this->privateRoot}/cvs/cv.pdf", 'pdf');

        Livewire::withQueryParams(['root' => 'private', 'path' => 'cvs'])
            ->test(ManageFiles::class)
            ->assertCanSeeTableRecords(['cvs/cv.pdf']);
    }

    public function test_a_path_outside_the_root_falls_back_to_the_root(): void
    {
        File::put("{$this->outside}/secret.txt", 'secret');

        Livewire::withQueryParams(['root' => 'public', 'path' => '../outside'])
            ->test(ManageFiles::class)
            ->assertSet('path', '')
            ->assertDontSee('secret.txt');
    }

    public function test_symlinks_that_leave_the_root_are_not_listed_or_reachable(): void
    {
        File::put("{$this->outside}/secret.txt", 'secret');
        symlink($this->outside, "{$this->publicRoot}/escape");

        Livewire::test(ManageFiles::class)->assertCanNotSeeTableRecords(['escape']);

        $this->expectException(FileManagerException::class);
        app(FileManager::class)->read('public', 'escape/secret.txt');
    }

    public function test_uploads_keep_their_name_and_never_overwrite(): void
    {
        File::put("{$this->publicRoot}/brochure.pdf", 'old');

        Livewire::test(ManageFiles::class)
            ->callTableAction('upload', data: ['files' => [UploadedFile::fake()->create('brochure.pdf', 10, 'application/pdf')]])
            ->assertNotified();

        $this->assertSame('old', File::get("{$this->publicRoot}/brochure.pdf"));
        $this->assertFileExists("{$this->publicRoot}/brochure (1).pdf");
    }

    #[TestWith(['shell.php'])]
    #[TestWith(['image.php.jpg'])]
    #[TestWith(['page.html'])]
    #[TestWith(['.htaccess'])]
    public function test_executable_uploads_are_refused(string $name): void
    {
        Livewire::test(ManageFiles::class)
            ->callTableAction('upload', data: ['files' => [UploadedFile::fake()->createWithContent($name, '<?php echo 1;')]]);

        $this->assertSame([], File::allFiles($this->publicRoot, hidden: true));
    }

    public function test_creates_folders_and_text_files(): void
    {
        Livewire::test(ManageFiles::class)
            ->callTableAction('newFolder', data: ['name' => 'reports'])
            ->assertHasNoFormErrors();

        Livewire::withQueryParams(['path' => 'reports'])
            ->test(ManageFiles::class)
            ->callTableAction('newFile', data: ['name' => 'notes.txt', 'contents' => 'Hello'])
            ->assertHasNoFormErrors();

        $this->assertSame('Hello', File::get("{$this->publicRoot}/reports/notes.txt"));
    }

    public function test_a_new_file_must_be_an_editable_type(): void
    {
        Livewire::test(ManageFiles::class)
            ->callTableAction('newFile', data: ['name' => 'run.php', 'contents' => '<?php phpinfo();'])
            ->assertNotified(__('admin.files.errors.extension_not_allowed', ['extensions' => implode(', ', config('site.file_manager.allowed_extensions'))]));

        $this->assertFileDoesNotExist("{$this->publicRoot}/run.php");
    }

    public function test_edits_a_text_file(): void
    {
        File::put("{$this->publicRoot}/robots.txt", 'old');

        Livewire::test(ManageFiles::class)
            ->mountTableAction('edit', 'robots.txt')
            ->assertSchemaStateSet(['contents' => 'old'])
            ->callMountedTableAction()
            ->assertHasNoFormErrors();

        Livewire::test(ManageFiles::class)
            ->callTableAction('edit', 'robots.txt', data: ['contents' => 'User-agent: *'])
            ->assertNotified(__('admin.files.notifications.saved'));

        $this->assertSame('User-agent: *', File::get("{$this->publicRoot}/robots.txt"));
    }

    public function test_binary_files_cannot_be_edited(): void
    {
        File::put("{$this->publicRoot}/photo.jpg", 'jpg');

        Livewire::test(ManageFiles::class)->assertTableActionHidden('edit', 'photo.jpg');

        $this->expectException(FileManagerException::class);
        app(FileManager::class)->write('public', 'photo.jpg', 'x');
    }

    public function test_renames_but_not_to_an_executable_extension(): void
    {
        File::put("{$this->publicRoot}/draft.txt", 'x');

        Livewire::test(ManageFiles::class)
            ->callTableAction('rename', 'draft.txt', data: ['name' => 'draft.php']);

        $this->assertFileExists("{$this->publicRoot}/draft.txt");

        Livewire::test(ManageFiles::class)
            ->callTableAction('rename', 'draft.txt', data: ['name' => 'final.txt'])
            ->assertNotified(__('admin.files.notifications.renamed'));

        $this->assertFileExists("{$this->publicRoot}/final.txt");
        $this->assertFileDoesNotExist("{$this->publicRoot}/draft.txt");
    }

    public function test_deletes_files_and_whole_folders(): void
    {
        File::put("{$this->publicRoot}/old.pdf", 'x');
        File::ensureDirectoryExists("{$this->publicRoot}/archive/2025");
        File::put("{$this->publicRoot}/archive/2025/report.pdf", 'x');

        Livewire::test(ManageFiles::class)
            ->callTableAction('delete', 'old.pdf')
            ->callTableAction('delete', 'archive');

        $this->assertSame([], File::allFiles($this->publicRoot));
        $this->assertDirectoryDoesNotExist("{$this->publicRoot}/archive");
    }

    public function test_the_root_folder_itself_cannot_be_deleted(): void
    {
        $this->expectException(FileManagerException::class);

        app(FileManager::class)->delete('public', '');
    }

    public function test_changes_permissions_to_an_allowed_mode(): void
    {
        File::put("{$this->privateRoot}/cv.pdf", 'x');
        chmod("{$this->privateRoot}/cv.pdf", 0644);

        Livewire::withQueryParams(['root' => 'private'])
            ->test(ManageFiles::class)
            ->callTableAction('permissions', 'cv.pdf', data: ['mode' => '0600'])
            ->assertHasNoFormErrors();

        clearstatcache();
        $this->assertSame('0600', substr(sprintf('%o', fileperms("{$this->privateRoot}/cv.pdf")), -4));
    }

    public function test_world_writable_permissions_are_refused(): void
    {
        File::put("{$this->publicRoot}/logo.svg", '<svg/>');

        Livewire::test(ManageFiles::class)
            ->callTableAction('permissions', 'logo.svg', data: ['mode' => '0777'])
            ->assertHasFormErrors(['mode']);

        $this->expectException(FileManagerException::class);
        app(FileManager::class)->changePermissions('public', 'logo.svg', '0777');
    }

    public function test_changes_are_written_to_the_audit_log(): void
    {
        File::put("{$this->publicRoot}/old.pdf", 'x');

        Log::shouldReceive('channel')->with('files')->andReturnSelf();
        Log::shouldReceive('info')->once()->with('file_manager.deleted', ['user' => $this->admin->email, 'root' => 'public', 'path' => 'old.pdf']);

        Livewire::test(ManageFiles::class)->callTableAction('delete', 'old.pdf');
    }

    public function test_only_admins_can_open_the_page(): void
    {
        $this->actingAs(User::factory()->create(['is_admin' => false, 'has_email_authentication' => true]))
            ->get(ManageFiles::getUrl())
            ->assertForbidden();
    }

    public function test_the_page_can_be_switched_off(): void
    {
        config(['site.file_manager.enabled' => false]);

        $this->get(ManageFiles::getUrl())->assertForbidden();
    }
}
