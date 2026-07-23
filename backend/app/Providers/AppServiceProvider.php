<?php

namespace App\Providers;

use App\Support\SqlQueryLogger;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        DB::listen(app(SqlQueryLogger::class)->handle(...));

        if (app()->runningInConsole() && ($_SERVER['argv'][1] ?? null) === 'serve') {
            echo PHP_EOL;
            echo '  Swagger: http://localhost:8000/docs/api#'.PHP_EOL;
            echo PHP_EOL;
        }

        Scramble::configure()->withDocumentTransformers(function (OpenApi $openApi): void {
            $openApi->secure(SecurityScheme::http('bearer'));
        });

        JsonResource::withoutWrapping();
    }
}
