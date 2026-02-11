<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Facility;
use App\Models\MultiImage;
use App\Models\RoomNumber;
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
       $basic_facility = Facility::where('rooms_id', $id)
                    ->pluck('facility_name')
                    ->toArray();
        $multiImages = MultiImage::where('rooms_id', $id)->get();
        
        $allRoomNo = RoomNumber::where('rooms_id', $id)->get();

        return view('backend.allroom.rooms.edit_rooms', compact('editData', 'basic_facility', 'multiImages', 'allRoomNo'));
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

    // Update multi image 
    if ($request->hasFile('multi_img')) {

        // 1. Supprimer les anciennes images (fichiers + DB)
         $oldImages = MultiImage::where('rooms_id', $id)->get();

        foreach ($oldImages as $img) {
            $oldPath = public_path('upload/rooming/multi_img/' . $img->multi_img);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
            $img->delete();
        }

       // 2. Enregistrer les nouvelles images
        foreach ($request->file('multi_img') as $file) {

            $imgName = hexdec(uniqid()) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('upload/rooming/multi_img/'), $imgName);

            $subImage = new MultiImage();
            $subImage->rooms_id = $room->id;
            $subImage->multi_img = $imgName;
            $subImage->save();
        }
    }
    
     return redirect()->back()->with([
                'message' => 'Room Updated Successfully',
                'alert-type' => 'success',
        ]);
    
    //  return redirect()->back()->with('success', 'Room updated successfully !');
   


  }

  
  public function MultiImageDelete($id)
  {
    // Récupérer l'image par ID
    $image = MultiImage::findOrFail($id);

    // Chemin de l'image
    $imagePath = public_path('upload/rooming/multi_img/' . $image->multi_img);

    // Supprimer le fichier s'il existe
    if (file_exists($imagePath)) {
        unlink($imagePath);
    }

    // Supprimer l'enregistrement en base de données
    $image->delete();

    return redirect()->back()->with([
        'message' => 'Multi Image Deleted Successfully',
        'alert-type' => 'success',
    ]);
   }

   public function StoreRoomNumber(Request $request, $id)
   {
        $data = new RoomNumber;
        $data->rooms_id = $id;
        $data->room_type_id = $request->room_type_id;
        $data->room_no = $request->room_no;
        $data->status = $request->status;
        $data->save();


          return redirect()->back()->with([
                'message' => 'Room Number Added Successfully',
                'alert-type' => 'success',
        ]);
   }
   public function EditRoomNumber($id)
   {
       $editRoomNo = RoomNumber::find($id);
       return view('backend.allroom.rooms.edit_room_no', compact('editRoomNo'));
   }

   public function UpdateRoomNumber(Request $request, $id)
   {
      $data = RoomNumber::find($id);
      $data->room_no = $request->room_no;
      $data->status = $request->status;
      $data->save();

        return redirect()->route('room.type.list')->with([
                'message' => 'Room Number Updated Successfully',
                'alert-type' => 'success',
        ]);
   }
   public function DeleteRoomNumber($id)
   {
      $data = RoomNumber::find($id);
      
      $data->delete();

        return redirect()->route('room.type.list')->with([
                'message' => 'Room Number Deleted Successfully',
                'alert-type' => 'success',
        ]);
   }
    
    

}

