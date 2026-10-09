<?php

namespace App\Http\Controllers;

use App\Support\Deployer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * POST /__deploy - the release bootstrap for hosts with no shell (see config/deploy.php).
 * A Wasmer `post-deployment` job can call it, or you can with curl/Postman after a release.
 */
class DeployController extends Controller
{
    public function __invoke(Request $request, Deployer $deployer): JsonResponse
    {
        $expected = (string) config('deploy.token');

        if ($expected !== '') {
            $given = (string) ($request->bearerToken() ?: $request->header('X-Deploy-Token'));

            // hash_equals: constant-time comparison, so the token can't be guessed byte by byte.
            abort_unless(hash_equals($expected, $given), 403, 'Invalid deploy token.');
        }

        if ($retryAfter = $this->secondsUntilNextRun()) {
            return response()->json(['ok' => false, 'message' => 'Deploy ran moments ago - try again shortly.'], 429)
                ->header('Retry-After', $retryAfter);
        }

        try {
            $log = $deployer->run((bool) config('deploy.demo'));
        } catch (Throwable $e) {
            // Details stay in the logs; the response doesn't leak internals.
            return response()->json(['ok' => false, 'message' => 'Deploy failed - check the application logs.'], 500);
        }

        return response()->json(['ok' => true, 'steps' => $log]);
    }

    /**
     * Rate limit without the cache or the database (a brand-new database has neither table yet):
     * a timestamp file is enough for "not more than once every few seconds".
     */
    private function secondsUntilNextRun(): int
    {
        $interval = (int) config('deploy.min_interval');
        $file = sys_get_temp_dir().DIRECTORY_SEPARATOR.'twtf-deploy-endpoint';

        $wait = $interval - (time() - (int) @filemtime($file));

        if ($interval > 0 && file_exists($file) && $wait > 0) {
            return $wait;
        }

        @touch($file);

        return 0;
    }
}
