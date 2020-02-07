<?php

namespace App\Http\Controllers;

use DB;
use App\Photo;
use App\Sharing;
use App\User;
use Validator;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Search User
    public function search(Request $r) {

        $validate = Validator::make($r->all() , [
            'search' => 'required|string'
        ]);

        if($validate->fails()) return response()->json($validate->errors(), 422);
        
        $data = []; $response = [];
        $search = explode(" ", $r->get('search'));
        foreach($search as $s) {
            $users = User::where('first_name', "LIKE", "%$s%")->orWhere('surname', 'LIKE', "%$s%")->orWhere('phone', 'LIKE', "%$s%")->get();
            foreach ($users as $user) if($this->user->id != $user->id) $data[$user->id] = [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'surname' => $user->surname,
                'phone' => $user->phone
            ];
        }
        
        foreach($data as $d) $response[] = $d;

        return response()->json($response, 200);

    }
	
	// user parse.
	public function wsr() {
		
		// Clear Database.
		DB::table('shares')->truncate();
		DB::table('photos')->truncate();
		DB::table('users')->truncate();
		
		// Find Users in WSR
		
		
		$users = DB::table('wsr.attendees')->get();
		foreach($users as $data) {
			$data->user_fio = iconv("utf-8", "windows-1252", $data->user_fio);
			$fio = explode(" ", $data->user_fio);
			// Create Users
			$user = new User();
			$user->first_name = $fio[0];
			$user->surname = $fio[1];
			$user->phone = rand(79000000000, 79999999999);
			$user->password = $data->user_password;
			// $user->api_token = Str::random(80);
			$user->save(); 

		}
		
		
		// Вывести всех пользователей.
		$users = User::get();
		$get = [];
		foreach($users as $user) {
			$get[] = $user->id;
			for($i = 1; $i <= 3; $i++) {
				$photo = new Photo();
				$photo->name = "Untitled";
				$photo->owner_id = $user->id;
				$photo->url = "http://".$_SERVER['SERVER_NAME']."/photos/photos/default/".$i.".jpg";
				$photo->save();
			}
			print("<hr> Создан пользователь <br> Имя: ".$user->first_name." <br> Фамилия: ".$user->surname." <br> Телефон: ".$user->phone." <br /> Пароль: ".$user->password);
		}
		
		$photos = Photo::get();
		foreach($photos as $photo) {
			foreach($get as $user) {
				$share = new Sharing();
				$share->photo_id = $photo->id;
				$share->user_id = $user;
				$share->save();
			}
		}
		
	}
}
