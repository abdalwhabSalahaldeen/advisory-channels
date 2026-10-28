<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Called by the GitHub deploy workflow right after it uploads a release: runs
 * what a terminal would (database updates, cache clearing, storage link).
 */
class DeployHookController extends Controller
{
    /**
     * Commands to run, and whether a failure should fail the deploy.
     *
     * @var array<string, array{parameters: array<string, mixed>, required: bool}>
     */
    private const COMMANDS = [
        'migrate' => ['parameters' => ['--force' => true], 'required' => true],
        'optimize:clear' => ['parameters' => [], 'required' => true],
        // Some hosts forbid symlinks; the site still works, only uploaded images would not show.
        'storage:link' => ['parameters' => ['--force' => true], 'required' => false],
    ];

    public function __invoke(Request $request): JsonResponse
    {
        $token = (string) config('site.deploy_token');

        abort_if($token === '' || ! hash_equals($token, (string) $request->bearerToken()), 404);

        $results = [];
        $failed = false;

        foreach (self::COMMANDS as $command => $settings) {
            try {
                $exitCode = Artisan::call($command, $settings['parameters']);
                $output = trim(Artisan::output());
            } catch (Throwable $exception) {
                report($exception);
                $exitCode = 1;
                $output = $exception->getMessage();
            }

            $results[] = ['command' => $command, 'exit_code' => $exitCode, 'output' => $output];
            $failed = $failed || ($exitCode !== 0 && $settings['required']);
        }

        Log::info('deploy.hook', ['results' => $results]);

        return response()->json(['ok' => ! $failed, 'results' => $results], $failed ? 500 : 200);
    }
}
