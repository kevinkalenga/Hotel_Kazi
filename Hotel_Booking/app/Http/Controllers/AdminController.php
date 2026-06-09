<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AdminController extends Controller
{
    public function AdminDashboard()
    {
        return view('admin.index');
    }

    public function AdminLogout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }
    public function AdminLogin()
    {
       
         return view('admin.admin_login');
    }
    public function AdminProfile()
    {
       
        //  get the user who is authenticated by id 
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_profile_view', compact('profileData'));
    }
    public function AdminProfileStore(Request $request)
    {
       $id = Auth::user()->id;
       $data = User::find($id);
       $data->name = $request->name;
       $data->email = $request->email;
       $data->phone = $request->phone;
       $data->address = $request->address;

       if($request->file('photo')) {
          $file = $request->file('photo');
        //   to remove an existing img before downloading
          @unlink(public_path('upload/admin_images/'.$data->photo));
          $filename = date('YmdHi').$file->getClientOriginalName();
          $file->move(public_path('upload/admin_images'), $filename);
          $data['photo'] = $filename;
       }

       $data->save();

      

        $notification = array(
           'message' => 'Admin Profile Updated Successfully',
           'alert-type' => 'success'
        );

       return redirect()->back()->with($notification);
     
    }

    public function AdminChangePassword()
    {
        $id = Auth::user()->id;
        $profileData = User::find($id);
        return view('admin.admin_change_password', compact('profileData'));
    }

    public function AdminPasswordUpdate(Request $request) 
    {
        // validation
       $request->validate([
          'old_password' => 'required',
          'new_password' => 'required|confirmed',
          
       ]);
        
      // check that old pwd and the new authenticated pwd match   
       if(!Hash::check($request->old_password, auth::user()->password)) {

            $notification = array(
              'message' => 'Old Password Does not Match!',
              'alert-type' => 'error'
            );

          return back()->with($notification);
    
    
        }

        // Update the new pwd 
        User::whereId(auth::user()->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        $notification = array(
           'message' => 'Your Password Is Updated Successfully',
           'alert-type' => 'success'
        );

       return back()->with($notification);
    }

    
    
    public function AllAdmin()
    {
       $allAdmin = User::where('role', 'admin')->get();
       return view('backend.pages.admin.all_admin', compact('allAdmin'));
    }
    
    
    public function AddAdmin()
    {
       $roles = Role::all();
       return view('backend.pages.admin.add_admin', compact('roles'));
    }
    public function StoreAdmin(Request $request)
    {
       $user = new User();
       $user->name = $request->name;
       $user->email = $request->email;
       $user->phone = $request->phone;
       $user->address = $request->address;
       $user->password = Hash::make($request->password);
       $user->role ='admin';
       $user->status = 'active';
       $user->save();

       if($request->roles) {
         $user->assignRole($request->roles);
       }

        $notification = array(
           'message' => 'Admin User Created Successfully',
           'alert-type' => 'success'
        );

         return redirect()->route('all.admin')->with($notification);
       
    }

    public function EditAdmin($id)
    {
      $user = User::find($id);
      $roles = Role::all();
      return view('backend.pages.admin.edit_admin',compact( 'user', 'roles'));
    }


   public function UpdateAdmin(Request $request,$id)
   {

        $user = User::find($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        $user->address = $request->address; 
        $user->role = 'admin';
        $user->status = 'active';
        $user->save();
        
        // to remove the previous data to assign new
        $user->roles()->detach();
        if ($request->roles) {
           $user->assignRole($request->roles);
        }

        $notification = array(
            'message' => 'Admin User Updated Successfully',
            'alert-type' => 'success'
        );

        return redirect()->route('all.admin')->with($notification); 

   }


   public function DeleteAdmin($id)
   {

        $user = User::find($id);
        if (!is_null($user)) {
            $user->delete();
        }

        $notification = array(
            'message' => 'Admin User Delete Successfully',
            'alert-type' => 'success'
        );

        return redirect()->back()->with($notification); 

    }
}
