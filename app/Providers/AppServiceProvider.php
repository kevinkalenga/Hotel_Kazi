<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SmtpSetting;
use Config;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider

{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    // public function boot(): void
    // {
        
    //      if (\Schema::hasTable('smtp_settings')) {
    //        $smtpsetting = SmtpSetting::first();

    //        if ($smtpsetting) {
    //           $data = [
    //             'driver' => $smtpsetting->mailer, 
    //             'host' => $smtpsetting->host,
    //             'port' => $smtpsetting->port,
    //             'username' => $smtpsetting->username,
    //             'password' => $smtpsetting->password,
    //             'from' => [
    //                 'address' => $smtpsetting->from_address,
    //                 'name' => 'Easyhotel'
    //             ]  
    //           ];
    //           Config::set('mail',$data);
    //        }

    //     }
    
    
    
    // }

     
    public function boot(): void
{
    URL::forceScheme('https');
    try {
        if (class_exists('Illuminate\Support\Facades\Schema') &&
            \Illuminate\Support\Facades\Schema::hasTable('smtp_settings')) {

            $smtpsetting = \App\Models\SmtpSetting::first();

            if ($smtpsetting) {
                \Config::set('mail', [
                    'driver' => $smtpsetting->mailer,
                    'host' => $smtpsetting->host,
                    'port' => $smtpsetting->port,
                    'username' => $smtpsetting->username,
                    'password' => $smtpsetting->password,
                    'from' => [
                        'address' => $smtpsetting->from_address,
                        'name' => 'Easyhotel'
                    ]
                ]);
            }
        }
    } catch (\Throwable $e) {
        // ne jamais casser l'app
    }
}
}
