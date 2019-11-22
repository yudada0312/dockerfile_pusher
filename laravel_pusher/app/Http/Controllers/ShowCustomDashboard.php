<?php

namespace App\Http\Controllers;

use BeyondCode\LaravelWebSockets\Statistics\Rules\AppId;
use Illuminate\Http\Request;
use Illuminate\Broadcasting\Broadcasters\PusherBroadcaster;
use Pusher\Pusher;

class ShowCustomDashboard
{
    public function index(Request $request)
    {
//        dd($_SERVER['HTTP_HOST']);
        return view('dashboard', [
            'apps' => config('websockets.apps'),
            'path' => config('websockets.path')
        ]);
    }

    public function event(Request $request)
    {
        $validated = $request->validate([
            'appId' => ['required', new AppId()],
            'key' => 'required',
            'secret' => 'required',
            'channel' => 'required',
            'event' => 'required',
            'host' => 'required',
            'force_tls' => 'required',
            'data' => 'json',
        ]);

       // dd($validated);

        $this->getPusherBroadcaster($validated)->broadcast(
            [$validated['channel']],
            $validated['event'],
            json_decode($validated['data'], true)
        );

        return 'ok';
    }

    protected function getPusherBroadcaster(array $validated): PusherBroadcaster
    {
        $options = [ 'cluster' => '',
                    'useTLS' => true,
                    'host' => $validated['host'],
                    'port' => 6001, //host 跑容器域名 所以port 固定
                    'scheme' => ($validated['force_tls']=='true') ? 'https' : 'http',
                    'curl_options' => [
                        CURLOPT_SSL_VERIFYHOST => 0,
                        CURLOPT_SSL_VERIFYPEER => 0,
                    ]
        ];
        $pusher = new Pusher(
            $validated['key'],
            $validated['secret'],
            $validated['appId'],
            $options
        );

        return new PusherBroadcaster($pusher);
    }
}
