<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallRequest;
use App\Support\Installer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

/**
 * The browser-based installer for hosting without a terminal.
 */
class InstallController extends Controller
{
    public function show(Request $request, Installer $installer): View
    {
        abort_unless($installer->acceptsToken($request->query('token')), 404);

        return view('install', [
            'token' => $request->query('token'),
            'requirements' => $installer->requirements(),
            'ready' => $installer->meetsRequirements(),
            'steps' => null,
        ]);
    }

    public function store(InstallRequest $request, Installer $installer): View
    {
        abort_unless($installer->meetsRequirements(), 422);

        return view('install', [
            'token' => null,
            'requirements' => [],
            'ready' => true,
            'steps' => $installer->install(
                $request->validated('name'),
                $request->validated('email'),
                $request->validated('password'),
            ),
        ]);
    }
}
