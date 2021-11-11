<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;
use Symfony\Component\Process\Process;

class ScanWebsite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $host;

    // The spy pixel list (trackers.txt) is seldom updated
    // If you wish to update it, you should change this
    // and then run phantom:cache_spy_pixels
    const VERSION = 0.2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $host)
    {
        $this->url = $url;
        $this->host = $host;
    }

    public static function hostCacheKey($host)
    {
        return 'host:' . self::VERSION . ':' . $host;
    }

    public static function trackerCacheKey($tracker)
    {
        return 'tracker:' . self::VERSION . ':' . $tracker;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $pdf = Browsershot::url($this->url)
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

        return true;
        //return (new Response(base64_decode($run->bodyHtml()), 200))->header('ContentType','application/pdf');
    }
}
