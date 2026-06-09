<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmtpSetting;
use App\Models\SiteSetting;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;


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

     
     public function SiteUpdate(Request $request)
{
    $site = SiteSetting::findOrFail($request->id);

    $request->validate([
        'phone'     => 'required|string|max:255',
        'address'   => 'required|string|max:255',
        'email'     => 'required|email',
        'facebook'  => 'nullable|string|max:255',
        'twitter'   => 'nullable|string|max:255',
        'instagram' => 'nullable|string|max:255',
        'copyright' => 'required|string|max:255',
        'logo'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
    ]);

    // Update les champs
    $site->phone     = $request->phone;
    $site->address   = $request->address;
    $site->email     = $request->email;
    $site->facebook  = $request->facebook;
    $site->twitter   = $request->twitter;
    $site->instagram = $request->instagram;
    $site->copyright = $request->copyright;

    // Gestion du logo
    if ($request->hasFile('logo')) {
        $logo = $request->file('logo');
        $name_gen = hexdec(uniqid()) . '.' . $logo->getClientOriginalExtension();
        $uploadPath = public_path('upload/site');

        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Supprime l'ancien logo
        if ($site->logo && file_exists(public_path($site->logo))) {
            unlink(public_path($site->logo));
        }

        // Déplace le fichier uploadé
        $logo->move($uploadPath, $name_gen);
        $site->logo = 'upload/site/' . $name_gen;
    }

    $site->save();

    return redirect()->back()->with([
        'message' => 'Site Setting Updated Successfully!',
        'alert-type' => 'success',
    ]);
}
}
