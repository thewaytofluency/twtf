<?php

namespace App\Http\Middleware;

use App\Support\Deployer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

/**
 * Self-healing release bootstrap for hosts without a shell. On the first web request after a
 * release (a new instance, new migrations, or a changed APP_RELEASE) it runs the Deployer once,
 * then leaves a marker so later requests pay nothing. Only active when AUTO_DEPLOY=true.
 */
class EnsureDeployed
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('deploy.auto') && ! file_exists($this->marker())) {
            $this->bootstrap();
        }

        return $next($request);
    }

    private function bootstrap(): void
    {
        $lock = fopen($this->marker().'.lock', 'c');

        try {
            // One request does the work; concurrent ones wait here, then see the marker and move on.
            flock($lock, LOCK_EX);

            if (file_exists($this->marker())) {
                return;
            }

            app(Deployer::class)->run((bool) config('deploy.demo'));
            file_put_contents($this->marker(), now()->toIso8601String());
        } catch (Throwable $e) {
            // The Deployer already logged it; the request carries on and the next one retries.
            report($e);
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }

    /** Per-release marker: new migrations or a new APP_RELEASE give a new name, so it runs again. */
    private function marker(): string
    {
        $fingerprint = md5(
            implode('|', array_map('basename', glob(database_path('migrations/*.php')) ?: []))
            .'|'.config('deploy.release')
            .'|'.(config('deploy.demo') ? 'demo' : 'plain')
        );

        return sys_get_temp_dir().DIRECTORY_SEPARATOR.'twtf-deployed-'.$fingerprint;
    }
}
