<?php

namespace App\Http\Controllers;

use Symfony;
use App\Services;

class GitController extends Controller
{
    function status()
    {

        $response = new Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $service = new Services\GitService();
            foreach ($service->packages() as $package) {
                $service->status($package);
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
