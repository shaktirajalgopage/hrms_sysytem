<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof QueryException) {
            return $this->handleQueryException($request, $e);
        }

        return parent::render($request, $e);
    }

    private function handleQueryException($request, QueryException $e)
    {
        $message = $this->friendlyDbMessage($e);

        // Always log the real error for developers
        \Log::error('Database error: ' . $e->getMessage());

        if ($request->expectsJson()) {
            return response()->json(['message' => $message], 422);
        }

        return redirect()->back()
            ->withInput()
            ->with('error', $message);
    }

    /**
     * Translate a QueryException into a human-friendly message
     * for ANY table/column, based on MySQL error code + parsed SQL state.
     */
    private function friendlyDbMessage(QueryException $e): string
    {
        $errorInfo = $e->errorInfo ?? [];
        $sqlState  = $errorInfo[0] ?? null;   // ANSI SQLSTATE
        $mysqlCode = $errorInfo[1] ?? null;   // MySQL-specific error code
        $driverMsg = $errorInfo[2] ?? $e->getMessage();

        return match (true) {

            // 22007 / 1292 — invalid datetime / invalid value for column type
            $sqlState === '22007' || $mysqlCode === 1292 =>
                $this->buildInvalidValueMessage($driverMsg),

            // 23000 / 1062 — duplicate entry (unique constraint)
            $mysqlCode === 1062 =>
                $this->buildDuplicateMessage($driverMsg),

            // 23000 / 1451 — can't delete/update: child row exists (FK constraint)
            $mysqlCode === 1451 =>
                'This record can\'t be deleted or changed because it is linked to other records.',

            // 23000 / 1452 — can't insert/update: parent row doesn't exist (FK constraint)
            $mysqlCode === 1452 =>
                'The related record you selected doesn\'t exist. Please refresh and try again.',

            // 1048 — column cannot be null
            $mysqlCode === 1048 =>
                $this->buildRequiredFieldMessage($driverMsg),

            // 1264 — value out of range for column
            $mysqlCode === 1264 =>
                'One of the values entered is too large or out of the allowed range.',

            // 1366 — incorrect string value (e.g. bad UTF-8 / emoji in non-utf8mb4 column)
            $mysqlCode === 1366 =>
                'One of the fields contains characters that aren\'t supported. Please remove any special symbols or emojis.',

            // 1406 — data too long for column
            $mysqlCode === 1406 =>
                'One of the values entered is too long for its field.',

            // 1054 — unknown column (usually a dev/config bug, not user error)
            $mysqlCode === 1054 =>
                'A system configuration issue prevented this from saving. Please contact support.',

            // 2002 / 2006 — can't connect to DB / server gone away
            in_array($mysqlCode, [2002, 2006]) =>
                'We\'re having trouble connecting to the database right now. Please try again shortly.',

            // Fallback — anything else
            default =>
                'Something went wrong while saving your data. Please check your input and try again.',
        };
    }

    private function buildInvalidValueMessage(string $driverMsg): string
    {
        // Try to extract the column name from: "Incorrect datetime value: '...' for column 'doj' at row 1"
        if (preg_match("/for column '([^']+)'/", $driverMsg, $m)) {
            $field = $this->prettifyFieldName($m[1]);
            return "The value entered for \"{$field}\" is not valid. Please check the format and try again.";
        }

        return 'One of the values entered is not valid. Please check your input and try again.';
    }

    private function buildDuplicateMessage(string $driverMsg): string
    {
        // Try to extract: "Duplicate entry 'foo@bar.com' for key 'employees.email_unique'"
        if (preg_match("/Duplicate entry '(.+)' for key '([^']+)'/", $driverMsg, $m)) {
            $value = $m[1];
            $key   = $m[2];

            // key is often like 'employees.email_unique' or just 'email_unique'
            $field = preg_replace('/^.*\./', '', $key);
            $field = preg_replace('/_unique$|_index$/', '', $field);
            $field = $this->prettifyFieldName($field);

            return "The value \"{$value}\" is already in use for \"{$field}\". Please use a different value.";
        }

        return 'This record already exists. Please use different, unique values.';
    }

    private function buildRequiredFieldMessage(string $driverMsg): string
    {
        // "Column 'email' cannot be null"
        if (preg_match("/Column '([^']+)' cannot be null/", $driverMsg, $m)) {
            $field = $this->prettifyFieldName($m[1]);
            return "\"{$field}\" is required and cannot be left empty.";
        }

        return 'A required field was left empty. Please fill in all required fields.';
    }

    private function prettifyFieldName(string $field): string
    {
        return ucwords(str_replace(['_', '-'], ' ', $field));
    }
}