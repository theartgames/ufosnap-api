<?php

namespace App\Http\Controllers;

use App\Jobs\ScanWebsite;
use Cache;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Spatie\Browsershot\Browsershot;
use Illuminate\Http\Response;

class PdfController extends Controller
{
    public function getPdfFromUrl(Request $request)
    {
        $host = $request->input('url');
        if (!preg_match("~^(?:f|ht)tps?://~i", $host)) {
            $url = 'https://' . $host;
        } else {
            $url = $host;
        }

        $validator = Validator::make(['url' => $url], [
            'url' => ['required', 'string', 'active_url'],
        ]);

        try {
            $validator->validate();
        } catch (\Exception $e) {
            return;
        }

        $pdf = Browsershot::url($url)
            ->setNodeBinary('/opt/homebrew/bin/node')
            //->setBinPath(app_path('Services/Browsershot/browser-local.js'))
            ->userAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36')
            ->waitUntilNetworkIdle();

        // Extra pieces required for Laravel Vapor
        //if (! App::environment(['local'])) {
            $pdf->setNodeBinary('/usr/bin/node')
                ->setIncludePath('$PATH:/usr/bin/node')
                ->setChromePath("/usr/bin/chromium-browser")
                ->setNodeModulePath('/usr/local/lib/node_modules');
                //->setBinPath(app_path('Services/Browsershot/browser-vapor.js'));
        //}

        return response($pdf->pdf(), 200)->header('Content-Type','application/pdf');
    }

    public function getPdfFromBody(Request $request)
    {
        $html = $request->getContent();

        $pdf = Browsershot::html($html)
            ->setNodeBinary('/opt/homebrew/bin/node')
            ->setBinPath(app_path('Services/Browsershot/browser-local.js'))
            ->userAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36')
            ->waitUntilNetworkIdle();

        // Extra pieces required for Laravel Vapor
        //if (! App::environment(['local'])) {
            $pdf->setNodeBinary('/usr/bin/node')
                ->setIncludePath('$PATH:/usr/bin/node')
                ->setChromePath("/usr/bin/chromium-browser")
                ->setNodeModulePath('/usr/local/lib/node_modules')
                ->setBinPath(app_path('Services/Browsershot/browser-vapor.js'));
        //}

        return response($pdf->pdf(), 200)->header('Content-Type','application/pdf');
    }
}
