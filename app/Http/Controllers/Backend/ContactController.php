<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Contact;


class ContactController extends Controller
{
    public function ContactUs()
    {
        return view('frontend.contact.contact_us');
    }

    public function StoreContactUs(Request $request){
        $request->validate([
          'name' => 'required|string|max:255',
          'email' => 'required|email',
          'phone' => 'nullable|string|max:20',
          'subject' => 'required|string|max:255',
          'message' => 'required|string',
        ]);

        Contact::create([
             'name' => $request->name,
             'email' => $request->email,
             'phone' => $request->phone,
             'subject' => $request->subject,
             'message' => $request->message,
            
        ]);

        $notification = array(
            'message' => 'Your message was sent successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

     }
}
