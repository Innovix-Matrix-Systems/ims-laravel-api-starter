<?php

namespace App\Http\Controllers\Health;

use App\Http\Controllers\Controller;
use App\Http\Services\Health\AppHealthService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HealthController extends Controller
{
    /**
      * __construct
      *
       * @return void
      */
    public function __construct(
        protected AppHealthService $appHealthService,
    ) {
    }

    public function healthz(Request $request)
    {

        $data = [
            "cache" => $this->appHealthService->isCacheTestSuccessful(),
            "http" => $this->appHealthService->isHttpTestSuccessful(),
            "storage" => $this->appHealthService->isStorageTestSuccessful(),
            "database" => $this->appHealthService->isDatabaseTestSuccessful(),
            "migration" => $this->appHealthService->isMigrationTestSuccessful(),
        ];
        return response($data, Response::HTTP_OK);
    }
}
