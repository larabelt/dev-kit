<?php

namespace App\Http\Controllers;

use Symfony;
use App\Services;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PhpUnitController extends Controller
{
    function test()
    {

        //need "data:" somehow... or another approach

        $response = new Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $service = new Services\PhpUnitService();
            foreach (['core', 'content'] as $package) {
                $service->test($package);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
