<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use Carbon\Carbon;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TeamController extends Controller
{
    public function AllTeam() 
    {
        // we access the table(teams) by the model Team
        $team = Team::latest()->get();
        return view('backend.team.all_team', compact('team'));
    }
    public function AddTeam() 
    {
       return view('backend.team.add_team');
    }
    
    // Request so as to get our request data
  public function StoreTeam(Request $request)
  {
    $request->validate([
        'name' => 'required|string|max:255',
        'position' => 'required|string|max:255',
        'facebook' => 'nullable|url',
        'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            $uploadPath = public_path('upload/team');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // ✅ Use the new Intervention Image v3 syntax
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $manager->read($image)
                ->resize(550, 670)
                ->save($uploadPath . '/' . $name_gen);

            $save_url = 'upload/team/' . $name_gen;
        } else {
            return back()->withErrors(['image' => 'No image uploaded.']);
        }

        Team::insert([
            'name' => $request->name,
            'position' => $request->position,
            'facebook' => $request->facebook,
            'image' => $save_url,
            'created_at' => now(),
        ]);

        return redirect()->route('all.team')->with([
            'message' => 'Team Data Inserted Successfully!',
            'alert-type' => 'success',
        ]);
    } catch (\Exception $e) {
        return back()->withErrors(['error' => $e->getMessage()]);
    }
   }


}
