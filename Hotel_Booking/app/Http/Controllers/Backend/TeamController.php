<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\BookArea;
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
        // ✅ Validate request inputs
        $request->validate([
            'name'      => 'required|string|max:255',
            'position'  => 'required|string|max:255',
            'facebook'  => 'nullable|url',
            'image'     => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

                $uploadPath = public_path('upload/team');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                // Use the new Intervention Image v3 syntax
                $manager = new ImageManager(new Driver());
                $manager->read($image)
                    ->resize(550, 670)
                    ->save($uploadPath . '/' . $name_gen);

                $save_url = 'upload/team/' . $name_gen;
            } else {
                return back()->withErrors(['image' => 'No image uploaded.']);
            }

            // Save to database
            Team::insert([
                'name'       => $request->name,
                'position'   => $request->position,
                'facebook'   => $request->facebook,
                'image'      => $save_url,
                'created_at' => Carbon::now(),
            ]);

            return redirect()->route('all.team')->with([
                'message' => 'Team Data Inserted Successfully!',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Something went wrong: ' . $e->getMessage(),
            ])->withInput();
        }
    }

    public function EditTeam($id)
    {
    //Find the specific team record or fail with 404
    $team = Team::findOrFail($id);

    //Return the edit view with the team data
    return view('backend.team.edit_team', compact('team'));
   }
  
  
  
  public function UpdateTeam(Request $request)
  {
    $team_id = $request->id; // hidden input in form
    $team = Team::findOrFail($team_id);

    $request->validate([
        'name'      => 'required|string|max:255',
        'position'  => 'required|string|max:255',
        'facebook'  => 'nullable|url',
        'image'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    try {
        $save_url = $team->image; // default to old image

        // If a new image is uploaded
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

            $uploadPath = public_path('upload/team');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Delete the old image file if it exists
            if ($team->image && file_exists(public_path($team->image))) {
                unlink(public_path($team->image));
            }

            // Resize & save new image using Intervention v3
            $manager = new \Intervention\Image\ImageManager(new \Intervention\Image\Drivers\Gd\Driver());
            $manager->read($image)
                ->resize(550, 670)
                ->save($uploadPath . '/' . $name_gen);

            $save_url = 'upload/team/' . $name_gen;
        }

        //Update team record
        $team->update([
            'name'      => $request->name,
            'position'  => $request->position,
            'facebook'  => $request->facebook,
            'image'     => $save_url,
            'updated_at'=> now(),
        ]);

        return redirect()->route('all.team')->with([
            'message' => 'Team Data Updated Successfully!',
            'alert-type' => 'success',
        ]);

    } catch (\Exception $e) {
        return back()->withErrors(['error' => 'Update failed: ' . $e->getMessage()]);
    }
}

   
   
   
   
   public function DeleteTeam($id)
   {
     try {
        //  1. Find the team record
        $team = Team::findOrFail($id);

        //  2. Delete the old image file (if it exists)
        if ($team->image && file_exists(public_path($team->image))) {
            unlink(public_path($team->image));
        }

        //  3. Delete the record from the database
        $team->delete();

        //  4. Redirect with success notification
        return redirect()->route('all.team')->with([
            'message' => 'Team Member Deleted Successfully!',
            'alert-type' => 'success',
        ]);

      } catch (\Exception $e) {
        //  Handle unexpected errors gracefully
        return back()->withErrors([
            'error' => 'Deletion failed: ' . $e->getMessage(),
        ]);
      }
    }

    // =============== Book Area All Methods (can also create a new controller)

    public function BookArea()
    {
        $book = BookArea::find(1);
        return view('backend.bookarea.book_area', compact('book'));
    }


}
