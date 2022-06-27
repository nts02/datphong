<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function getListRoomType(){
        $room_types = RoomType::all();
        return response()->json($room_types);
    }

    public function getListRoom(){
        $room = Room::all();
        return response()->json($room);
    }

}
