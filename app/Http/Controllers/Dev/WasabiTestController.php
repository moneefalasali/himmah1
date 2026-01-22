<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WasabiTestController extends Controller
{
    public function index(Request $request)
    {
        if (!config('app.debug')) {
            abort(403, 'Dev-only route');
        }

        $path = 'dev/wasabi_check_'.time().'.txt';
        try {
            $putResult = Storage::disk('wasabi')->put($path, 'ok '.now());
            $exists = false;
            $existsError = null;
            try {
                $exists = Storage::disk('wasabi')->exists($path);
            } catch (\Throwable $ex) {
                $existsError = $ex->getMessage();
            }

            // try to generate temporary URL if supported
            $url = null;
            $urlError = null;
            try {
                if (method_exists(Storage::disk('wasabi'), 'temporaryUrl')) {
                    $url = Storage::disk('wasabi')->temporaryUrl($path, now()->addMinutes(10));
                }
            } catch (\Throwable $ex) {
                $urlError = $ex->getMessage();
            }

            return response()->json([
                'success' => true,
                'path' => $path,
                'put_result' => $putResult,
                'exists' => $exists,
                'exists_error' => $existsError,
                'temp_url' => $url,
                'temp_url_error' => $urlError,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'exception_class' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
