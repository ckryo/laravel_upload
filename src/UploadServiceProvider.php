<?php

namespace Ckryo\Laravel\Upload;

use Ckryo\Laravel\Http\ErrorCode;
use Illuminate\Support\ServiceProvider;

class UploadServiceProvider extends ServiceProvider
{

    /**
     * Register any application authentication / authorization services.
     *
     * @return void
     */
    public function boot(ErrorCode $errorCode)
    {
        $this->loadRoutesFrom(__DIR__.'/routes.php');
    }

}
