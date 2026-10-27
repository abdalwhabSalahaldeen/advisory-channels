<?php

namespace App\Support;

use App\Exceptions\FileManagerException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Browses and changes the files under the roots in config('site.file_manager.roots').
 *
 * Every path is relative to a root and is checked so that it can never leave it
 * (no "..", no symlinks pointing outside). Hidden files, the framework's own
 * folders and executable extensions are out of reach.
 */
class FileManager
{
    /**
     * Extensions that a web server might execute, refused anywhere in a file name.
     */
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar', 'pht', 'phps',
        'cgi', 'pl', 'py', 'sh', 'bash', 'exe', 'bat', 'cmd', 'asp', 'aspx', 'jsp',
        'shtml', 'htaccess', 'htpasswd', 'html', 'htm', 'js', 'mjs',
    ];

    /**
     * @return array<string, string> root key => absolute path
     */
    public function roots(): array
    {
        return config('site.file_manager.roots');
    }

    public function hasRoot(string $root): bool
    {
        return array_key_exists($root, $this->roots());
    }

    /**
     * @return list<array{path: string, name: string, type: 'directory'|'file', extension: ?string, size: ?int, modified: int, permissions: string, symbolic: string, owner: string, group: string, readable: bool, writable: bool, editable: bool, image: bool, url: ?string}>
     */
    public function list(string $root, string $path = ''): array
    {
        $directory = $this->absolute($root, $path);

        if (! is_dir($directory)) {
            throw FileManagerException::because('not_found');
        }

        $entries = [];

        foreach (scandir($directory) ?: [] as $name) {
            if ($name === '.' || $name === '..' || $this->isHidden($name)) {
                continue;
            }

            try {
                $entries[] = $this->describe($root, ltrim("{$this->normalize($path)}/{$name}", '/'));
            } catch (FileManagerException) {
                // A symlink that points outside the root is left out instead of failing the listing.
                continue;
            }
        }

        // Folders first, then files, each alphabetically.
        usort($entries, fn (array $a, array $b): int => [$a['type'] !== 'directory', mb_strtolower($a['name'])] <=> [$b['type'] !== 'directory', mb_strtolower($b['name'])]);

        return $entries;
    }

    /**
     * @return array{path: string, name: string, type: 'directory'|'file', extension: ?string, size: ?int, modified: int, permissions: string, symbolic: string, owner: string, group: string, readable: bool, writable: bool, editable: bool, image: bool, url: ?string}
     */
    public function describe(string $root, string $path): array
    {
        $absolute = $this->absolute($root, $path);

        if (! file_exists($absolute)) {
            throw FileManagerException::because('not_found');
        }

        clearstatcache(true, $absolute);

        $isDirectory = is_dir($absolute);
        $extension = $isDirectory ? null : (mb_strtolower(pathinfo($absolute, PATHINFO_EXTENSION)) ?: null);
        $mode = fileperms($absolute);

        return [
            'path' => $this->normalize($path),
            'name' => basename($absolute),
            'type' => $isDirectory ? 'directory' : 'file',
            'extension' => $extension,
            'size' => $isDirectory ? null : filesize($absolute),
            'modified' => filemtime($absolute),
            'permissions' => substr(sprintf('%o', $mode), -4),
            'symbolic' => $this->symbolic($mode),
            'owner' => $this->ownerName(fileowner($absolute)),
            'group' => $this->groupName(filegroup($absolute)),
            'readable' => is_readable($absolute),
            'writable' => is_writable($absolute),
            'editable' => ! $isDirectory && in_array($extension, config('site.file_manager.editable_extensions'), true),
            'image' => in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif', 'svg', 'ico'], true),
            'url' => ! $isDirectory && $root === 'public' ? Storage::disk('public')->url($this->normalize($path)) : null,
        ];
    }

    /**
     * Stores an uploaded file in the folder under its original (cleaned) name.
     *
     * @return string the new file's path
     */
    public function upload(string $root, string $directory, UploadedFile $file): string
    {
        if ($file->getSize() > config('site.file_manager.max_upload_kb') * 1024) {
            throw FileManagerException::because('too_large', ['max' => config('site.file_manager.max_upload_kb') / 1024]);
        }

        $name = $this->cleanFileName($file->getClientOriginalName());
        $this->assertAllowedFileName($name);

        $target = $this->uniquePath($this->absoluteDirectory($root, $directory), $name);
        File::copy($file->getRealPath(), $target);

        $path = $this->relative($root, $target);
        $this->log('uploaded', $root, $path);

        return $path;
    }

    public function createDirectory(string $root, string $directory, string $name): string
    {
        $this->assertValidName($name);

        $target = $this->absoluteDirectory($root, $directory).'/'.$name;

        if (file_exists($target)) {
            throw FileManagerException::because('exists');
        }

        File::makeDirectory($target, 0755);

        $path = $this->relative($root, $target);
        $this->log('directory_created', $root, $path);

        return $path;
    }

    public function createFile(string $root, string $directory, string $name, string $contents = ''): string
    {
        $this->assertAllowedFileName($name);
        $this->assertEditable($name);
        $this->assertEditableSize($contents);

        $target = $this->absoluteDirectory($root, $directory).'/'.$name;

        if (file_exists($target)) {
            throw FileManagerException::because('exists');
        }

        File::put($target, $contents);

        $path = $this->relative($root, $target);
        $this->log('file_created', $root, $path);

        return $path;
    }

    public function read(string $root, string $path): string
    {
        $absolute = $this->absoluteFile($root, $path);
        $this->assertEditable($absolute);

        if (filesize($absolute) > config('site.file_manager.max_edit_kb') * 1024) {
            throw FileManagerException::because('too_large_to_edit', ['max' => config('site.file_manager.max_edit_kb')]);
        }

        return File::get($absolute);
    }

    public function write(string $root, string $path, string $contents): void
    {
        $absolute = $this->absoluteFile($root, $path);
        $this->assertEditable($absolute);
        $this->assertEditableSize($contents);

        if (! is_writable($absolute)) {
            throw FileManagerException::because('not_writable');
        }

        File::put($absolute, $contents);

        $this->log('file_edited', $root, $path);
    }

    public function rename(string $root, string $path, string $name): string
    {
        $absolute = $this->absoluteExisting($root, $path);

        if (is_dir($absolute)) {
            $this->assertValidName($name);
        } else {
            $this->assertAllowedFileName($name);
        }

        $target = dirname($absolute).'/'.$name;

        if (file_exists($target)) {
            throw FileManagerException::because('exists');
        }

        File::move($absolute, $target);

        $newPath = $this->relative($root, $target);
        $this->log('renamed', $root, $path, ['to' => $newPath]);

        return $newPath;
    }

    public function delete(string $root, string $path): void
    {
        $absolute = $this->absoluteExisting($root, $path);

        if (is_dir($absolute)) {
            File::deleteDirectory($absolute);
        } else {
            File::delete($absolute);
        }

        $this->log('deleted', $root, $path);
    }

    public function changePermissions(string $root, string $path, string $mode): void
    {
        $absolute = $this->absoluteExisting($root, $path);
        $allowed = config(is_dir($absolute) ? 'site.file_manager.directory_modes' : 'site.file_manager.file_modes');

        if (! in_array($mode, $allowed, true)) {
            throw FileManagerException::because('mode_not_allowed');
        }

        if (! @chmod($absolute, octdec($mode))) {
            throw FileManagerException::because('chmod_failed');
        }

        $this->log('permissions_changed', $root, $path, ['mode' => $mode]);
    }

    /**
     * Absolute path of a root-relative path, which must stay inside the root.
     */
    public function absolute(string $root, string $path = ''): string
    {
        if (! $this->hasRoot($root)) {
            throw FileManagerException::because('not_found');
        }

        $base = $this->rootPath($root);
        $relative = $this->normalize($path);

        foreach (explode('/', $relative) as $segment) {
            if ($segment !== '' && $this->isHidden($segment)) {
                throw FileManagerException::because('not_found');
            }
        }

        $absolute = $relative === '' ? $base : "{$base}/{$relative}";

        // Resolve symlinks of whatever already exists and make sure it is still inside the root.
        $existing = $absolute;

        while (! file_exists($existing) && $existing !== $base) {
            $existing = dirname($existing);
        }

        $real = realpath($existing);

        if ($real === false || ($real !== $base && ! str_starts_with($real, $base.'/'))) {
            throw FileManagerException::because('outside_root');
        }

        return $absolute;
    }

    private function rootPath(string $root): string
    {
        $path = $this->roots()[$root];

        File::ensureDirectoryExists($path);

        return realpath($path);
    }

    private function absoluteDirectory(string $root, string $directory): string
    {
        $absolute = $this->absolute($root, $directory);

        if (! is_dir($absolute)) {
            throw FileManagerException::because('not_found');
        }

        return $absolute;
    }

    private function absoluteFile(string $root, string $path): string
    {
        $absolute = $this->absoluteExisting($root, $path);

        if (! is_file($absolute)) {
            throw FileManagerException::because('not_found');
        }

        return $absolute;
    }

    /**
     * An existing file or folder inside the root, never the root itself.
     */
    private function absoluteExisting(string $root, string $path): string
    {
        if ($this->normalize($path) === '') {
            throw FileManagerException::because('root_protected');
        }

        $absolute = $this->absolute($root, $path);

        if (! file_exists($absolute)) {
            throw FileManagerException::because('not_found');
        }

        return $absolute;
    }

    private function relative(string $root, string $absolute): string
    {
        return ltrim(Str::after($absolute, $this->rootPath($root)), '/');
    }

    private function normalize(string $path): string
    {
        if (str_contains($path, "\0")) {
            throw FileManagerException::because('outside_root');
        }

        $segments = [];

        foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                throw FileManagerException::because('outside_root');
            }

            $segments[] = $segment;
        }

        return implode('/', $segments);
    }

    private function isHidden(string $name): bool
    {
        return str_starts_with($name, '.') || in_array($name, config('site.file_manager.hidden'), true);
    }

    private function assertValidName(string $name): void
    {
        if ($name === '' || mb_strlen($name) > 150 || $this->isHidden($name) || preg_match('/[\/\\\\:*?"<>|\x00-\x1F]/u', $name)) {
            throw FileManagerException::because('invalid_name');
        }
    }

    private function assertAllowedFileName(string $name): void
    {
        $this->assertValidName($name);

        $parts = array_map('mb_strtolower', explode('.', $name));
        $extension = count($parts) > 1 ? end($parts) : null;

        // Refuse "report.php.pdf" too: some server set-ups execute any ".php." in a name.
        if (array_intersect(array_slice($parts, 1), self::BLOCKED_EXTENSIONS) !== []
            || ! in_array($extension, config('site.file_manager.allowed_extensions'), true)) {
            throw FileManagerException::because('extension_not_allowed', ['extensions' => implode(', ', config('site.file_manager.allowed_extensions'))]);
        }
    }

    private function assertEditable(string $name): void
    {
        if (! in_array(mb_strtolower(pathinfo($name, PATHINFO_EXTENSION)), config('site.file_manager.editable_extensions'), true)) {
            throw FileManagerException::because('not_editable');
        }
    }

    private function assertEditableSize(string $contents): void
    {
        if (strlen($contents) > config('site.file_manager.max_edit_kb') * 1024) {
            throw FileManagerException::because('too_large_to_edit', ['max' => config('site.file_manager.max_edit_kb')]);
        }
    }

    private function cleanFileName(string $name): string
    {
        $name = trim(preg_replace('/[\/\\\\:*?"<>|\x00-\x1F]+/u', '-', basename($name)), ' .-');

        return $name === '' ? Str::random(8) : $name;
    }

    /**
     * "report.pdf", then "report (1).pdf", "report (2).pdf"... so nothing is overwritten.
     */
    private function uniquePath(string $directory, string $name): string
    {
        $base = pathinfo($name, PATHINFO_FILENAME);
        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $candidate = "{$directory}/{$name}";

        for ($copy = 1; file_exists($candidate); $copy++) {
            $candidate = "{$directory}/{$base} ({$copy}).{$extension}";
        }

        return $candidate;
    }

    private function symbolic(int $mode): string
    {
        $symbolic = ($mode & 0x4000) ? 'd' : '-';

        foreach ([0400, 0200, 0100, 040, 020, 010, 04, 02, 01] as $index => $bit) {
            $symbolic .= ($mode & $bit) ? 'rwx'[$index % 3] : '-';
        }

        return $symbolic;
    }

    private function ownerName(int|false $id): string
    {
        if ($id === false) {
            return '?';
        }

        return function_exists('posix_getpwuid') ? (posix_getpwuid($id)['name'] ?? (string) $id) : (string) $id;
    }

    private function groupName(int|false $id): string
    {
        if ($id === false) {
            return '?';
        }

        return function_exists('posix_getgrgid') ? (posix_getgrgid($id)['name'] ?? (string) $id) : (string) $id;
    }

    /**
     * @param  array<string, mixed>  $context
     */
    private function log(string $action, string $root, string $path, array $context = []): void
    {
        Log::channel('files')->info("file_manager.{$action}", [
            'user' => auth()->user()?->email,
            'root' => $root,
            'path' => $path,
            ...$context,
        ]);
    }
}
