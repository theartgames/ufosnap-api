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

        $pdf = Browsershot::html($html)
            ->setNodeBinary($this->getNodeBinary())
            ->setBinPath($this->getBinPath())
            ->userAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36')
            ->setIncludePath($this->getIncludePath())
            ->setChromePath($this->getChromePath())
            ->setNodeModulePath($this->getNodeModulePath())
            ->waitUntilNetworkIdle();

        return response($pdf->pdf(), 200)->header('Content-Type','application/pdf');
    }

    public function getPdfFromBody(Request $request)
    {
        $html = $request->getContent();

        $pdf = Browsershot::html($html)
            ->setNodeBinary($this->getNodeBinary())
            ->setBinPath($this->getBinPath())
            ->userAgent('Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/78.0.3904.108 Safari/537.36')
            ->setIncludePath($this->getIncludePath())
            ->setChromePath($this->getChromePath())
            ->setNodeModulePath($this->getNodeModulePath())
            ->waitUntilNetworkIdle();

        return response($pdf->pdf(), 200)->header('Content-Type','application/pdf');
    }

    protected function getNodeBinary()
    {
        if(! App::environment(['local'])) {
            return '/usr/bin/node';
        } else {
            return '/opt/homebrew/bin/node';
        }
    }

    protected function getBinPath(Browsershot $browsershot)
    {
        if(! App::environment(['local'])) {
            return app_path('Services/Browsershot/browser-vapor.js');
        } else {
            return app_path('Services/Browsershot/browser-local.js');
        }
    }

    protected function getIncludePath()
    {
        if(! App::environment(['local'])) {
            return '$PATH:/usr/bin/node';
        } else {
            return null;
        }
    }
    protected function getChromePath()
    {
        if(! App::environment(['local'])) {
            return '/usr/bin/chromium-browser';
        } else {
            return null;
        }
    }
    protected function getNodeModulePath()
    {
        if(! App::environment(['local'])) {
            return '/usr/local/lib/node_modules';
        } else {
            return null;
        }
    }
}
