<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{

    public function check(Request $request){

        $startDate = $request->checkin;
        $endDate = $request->checkout;

        $diffDate = Carbon::parse($request->checkout)->diffInDays(Carbon::parse($request->checkin));
        $room = DB::table('rooms')->where('id',(int)$request->room_id)->first();


        $total_money = $diffDate * floatval($room->price);

        $booking = new Booking;
        $booking->full_name = $request->full_name;
        $booking->phone = $request->phone;
        $booking->email = $request->email;
        $booking->quantity = $request->quantity;
        $booking->checkin = $startDate;
        $booking->checkout = $endDate;
        $booking->room_id = $request->room_id;
        $booking->total_money = $total_money;

        $count = Booking::where('room_id',$request->room_id)->count();
        if($count == 0){
            $result = $booking->save();
//            $room = Room::findOrFail($request->room_id);
//            $roomName = $room->room_code;
            if($result){
                $client = new Client();
                $res = $client->request('GET','http://127.0.0.1:8001/api/notification/1');

                $send = new Client();
                $resEmail = $send->request('POST','http://127.0.0.1:8001/api/sendmail',[
                    'json' => [
                        'id' =>  $booking->id,
                        'full_name'=> $request->full_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'checkin'=> $request->checkin,
                        'checkout'=> $request->checkout,
                        'quantity' => $request->quantity,
                        'room_id'=>$request->room_id,
                        'money'=>$total_money
                    ]
                ]);

                return response()->json(array(
                    'notification' => json_decode($res->getBody()->getContents()),
                    'send_mail' => $resEmail->getBody()->getContents(),
                ));
            }
        }

        $check = Booking::where('room_id',$request->room_id)
            ->Where("checkout",'<',$startDate)
            ->orWhere("checkin",'>',$endDate)
            ->count();

        if($check == $count ) {
            $result = $booking->save();
            if($result){

                $client = new Client();
                $res = $client->request('GET','http://127.0.0.1:8001/api/notification/1');

                $send = new Client();
                $resEmail = $send->request('POST','http://127.0.0.1:8001/api/sendmail',[
                    'json' => [
                        'id' =>  $booking->id,
                        'full_name'=> $request->full_name,
                        'phone' => $request->phone,
                        'email' => $request->email,
                        'checkin'=> $request->checkin,
                        'checkout'=> $request->checkout,
                        'quantity' => $request->quantity,
                        'room_id'=>$request->room_id,
                        'money'=>$total_money
                    ]
                ]);

                return response()->json(array(
                    'notification' => json_decode($res->getBody()->getContents()),
                    'send_mail' => $resEmail->getBody()->getContents(),
                ));
            }
        }

        $client = new Client();
        $res = $client->request('GET', 'http://127.0.0.1:8001/api/notification/2');
        $response = json_decode($res->getBody()->getContents());
        unset($response->id);
        // cho vao json
        return response()->json($response);
    }

}
