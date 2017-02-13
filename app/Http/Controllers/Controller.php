<?php

namespace App\Http\Controllers;

use Symfony;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    function foo()
    {

        $response = new Symfony\Component\HttpFoundation\StreamedResponse(function () {
            $i = 0;
            while ($i < 5) {
                $time = date('r');
                echo "data: The server time is: {$time}\n\n";
                ob_flush();
                flush();
                sleep(1);
                $i++;
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('X-Accel-Buffering', 'no');

        return $response;
    }
}
