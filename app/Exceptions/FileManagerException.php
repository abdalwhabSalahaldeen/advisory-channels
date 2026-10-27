<?php

namespace App\Exceptions;

use Exception;

/**
 * A file manager operation the dashboard refused; the message is shown to the admin.
 */
class FileManagerException extends Exception
{
    public static function because(string $reason, array $replace = []): self
    {
        return new self(__("admin.files.errors.{$reason}", $replace));
    }
}
