<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmtpSetting;
use App\Models\SiteSetting;

class SettingController extends Controller
{
    public function SmtpSetting() 
    {
        $smtp = SmtpSetting::find(1);
        return view('backend.setting.smtp_update', compact('smtp'));
    }
    public function SmtpUpdate(Request $request) 
    {
        // recup de l'id depuis le template
        $smtp_id = $request->id;

        SmtpSetting::find($smtp_id)->update([
            'mailer' => $request->mailer,
            'host' => $request->host,
            'port' => $request->port,
            'username' => $request->username,
            'password' => $request->password,
            'from_address' => $request->from_address,
        ]);


         $notification = array(
           'message' => 'Smtp Setting Updated Successfully',
           'alert-type' => 'success'
        );

       return redirect()->back()->with($notification);
    }

    public function SiteSetting()
    {
         $site = SiteSetting::find(1);
        return view('backend.site.site_update',compact('site'));
    }
}
