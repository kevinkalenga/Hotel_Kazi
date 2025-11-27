<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Facility;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Carbon\Carbon;


class RoomController extends Controller
{
    public function EditRoom($id)
    {
       // Get the room by ID
    $editData = Room::findOrFail($id);

    // Decode the facility_name JSON column (assuming it stores multiple facilities)
    // If it’s stored as plain text for single facility, you can wrap it in an array
    $basic_facility = json_decode($editData->facility_name, true) ?? [$editData->facility_name];

    return view('backend.allroom.rooms.edit_rooms', compact('editData', 'basic_facility'));
    }


   public function updateRoom(Request $request, $id)
   {
    $room = Room::findOrFail($id);
     // all our db fields first and all request from the template
    // Mise à jour des champs simples
    $room->total_adult     = $request->total_adult;
    $room->total_child     = $request->total_child;
    $room->room_capacity   = $request->room_capacity;
    $room->price           = $request->price;
    $room->size            = $request->size;
    $room->view            = $request->view;
    $room->bed_style       = $request->bed_style;
    $room->discount        = $request->discount;
    $room->short_desc      = $request->short_desc;
    $room->description     = $request->description;

    // Gestion de l'image
    if ($request->hasFile('image')) {

        // Supprimer l'ancienne image
        if ($room->image && file_exists(public_path($room->image))) {
            unlink(public_path($room->image));
        }

        $image = $request->file('image');
        $name_gen = hexdec(uniqid()) . '.' . $image->getClientOriginalExtension();

        $uploadPath = public_path('upload/rooming');

        // Créer le dossier si nécessaire
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Intervention Image V3
        $manager = new ImageManager(new Driver());
        $manager->read($image)
                ->resize(550, 850)
                ->save($uploadPath . '/' . $name_gen);

        // Enregistrer chemin image
        $room->image = 'upload/rooming/' . $name_gen;
    }

    // Sauvegarde en DB
    $room->save();

    //  Update for Facility Table 
    if($request->facility_name == NULL) {
         return redirect()->back()->with([
                'message' => 'Sorry! Not Any Basic Facility Select',
                'alert-type' => 'error',
        ]);
    } else {
        Facility::where('rooms_id', $id)->delete();
        $faciities = count($request->facility_name);

        for($i=0; $i < $faciities; $i++) {
            $fcount = new Facility();
            $fcount->rooms_id = $room->id;
            $fcount->facility_name = $request->facility_name[$i];
            $fcount->save();
        }
    }
    
    
    
     return redirect()->back()->with('success', 'Room updated successfully !');
   


  }

    
    

}

