<?php

declare(strict_types=1);

namespace App\Providers;

use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\Operation;
use Dedoc\Scramble\Support\Generator\Parameter;
use Dedoc\Scramble\Support\Generator\Schema;
use Dedoc\Scramble\Support\Generator\Types\StringType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Model::shouldBeStrict();
        JsonResource::withoutWrapping();
        $this->configureApiDocumentation();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }

    private function configureApiDocumentation(): void
    {
        Scramble::configure()
            ->withOperationTransformers(function (Operation $operation): void {
                $operation->addParameters([
                    Parameter::make('X-Tenant-Domain', 'header')
                        ->setSchema(Schema::fromType(new StringType))
                        ->required(true)
                        ->description('Domínio da instituição (tenant). Obrigatório, exceto quando o domínio vier no claim `X-Domain` do token JWT.')
                        ->example('loja-alfa'),
                ]);
            });
    }
}
