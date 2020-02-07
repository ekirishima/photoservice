<?php

namespace App\Http\Controllers;

use Storage;
use App\Photo;
use App\Sharing;
use Validator;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PhotosController extends Controller
{
    
    // Загрузка фотографии.
    public function upload(Request $r) {

        $validate = Validator::make($r->all(), [
            'photo' => 'required|mimes:jpeg,jpg,png|max:5000'
        ]);

        if($validate->fails()) return response()->json($validate->errors(), 422);
        
        $path_2 = Str::random(10).".png";
        $path_1 = Str::random(10);

        Storage::disk('photos')->putFileAs($path_1, $r->file('photo'), $path_2);
        
        $photo = new Photo();
        $photo->name = $r->get('name') ?? "Untitled";
        $photo->owner_id = $this->user->id;
        $photo->url = "http://".$_SERVER['SERVER_NAME']."/photos/photos/".$path_1."/".$path_2;
        $photo->save();

        return response()->json([
            'id' => $photo->id,
            'name' => $photo->name,
            'url' => $photo->url
        ], 201);

    }

    // Удаление фотографии.
    public function edit($id, Request $r) {
        if($r->isMethod('post')) return response()->json(['_method' => 'patch method required!'], 422);
        $photo = Photo::find($id);
        if(!$photo) return response()->json("", 404);
        if($photo->owner_id != $this->user->id) return response()->json("", 403);
        $photo->name = $r->get('name') ?? $photo->name ?? "Untitled";
        if($r->get('photo')) {
            $data = explode('base64,', $r->get('photo'));
            if($data[1]) {
                $path_2 = Str::random(20).".png";
                Storage::disk('photos')->put($path_2, base64_decode($data[1]));
                $photo->url = "http://".$_SERVER['SERVER_NAME']."/photos/photos/".$path_2;
            }
        }
        $photo->save();
        return response()->json([
            'id' => $photo->id,
            'name' => $photo->name,
            'url' => $photo->url
        ], 201);
    } 

    // Получение всех фотографий.
    public function index() {
        $data = []; $response = [];
        $me = Photo::where('owner_id', $this->user->id)->get();
        $sharing = Sharing::where('user_id', $this->user->id)->get();
        foreach($me as $photo) $data[$photo->id] = [
            'id' => $photo->id,
            'name' => $photo->name,
            'url' => $photo->url,
			'owner_id' => $photo->owner_id,
            'users' => Sharing::where('photo_id', $photo->id)->pluck('user_id')
        ];
        foreach($sharing as $photo) $data[$photo->photo->id] = [
            'id' => $photo->photo->id,
            'name' => $photo->photo->name,
            'url' => $photo->photo->url,
			'owner_id' => $photo->photo->owner_id,
            'users' => Sharing::where('photo_id', $photo->photo->id)->pluck('user_id')
        ];
        foreach($data as $photo) $response[] = $photo;
        return response()->json($response, 200);
    }

    // Получение одной фотографии
    public function photo($id) {
        $photo = Photo::find($id); $access = false;
        if(!$photo) return response()->json("", 404);
        if($photo->owner_id == $this->user->id) $access = true;
        else if(Sharing::where('photo_id', $photo->id)->where('user_id', $this->user->id)->first()) $access = true;
        
        if(!$access) return response()->json("", 403);
        
        return response()->json([
            'id' => $photo->id,
            'name' => $photo->name,
            'url' => $photo->url,
            'owner_id' => $photo->owner_id,
            'users' => Sharing::where('photo_id', $photo->id)->pluck('user_id')
        ], 200);
    }

    // Удаление фотографии
    public function delete($id) {
        $photo = Photo::find($id);
        if(!$photo) return response()->json("", 404);
        if($photo->owner_id != $this->user->id) return response()->json("", 403);
        Sharing::where('photo_id', $photo->id)->delete();
        $photo->delete();
        return response()->json("", 204);
    }

    // Шаринг фотографии
    public function share($user, Request $r) {

        $validate = Validator::make($r->all() , [
            'photos' => 'required|array'
        ]);

        if($validate->fails()) return response()->json($validate->errors(), 422);

        $existing_photos = [];

        $user = User::find($user);
        if(!$user) return response()->json("", 404);
        
        foreach($r->get('photos') as $id) {
            $photo = Photo::find($id);
            if(!$photo) continue; // Получение фото и прав.
            if($photo->owner_id != $this->user->id) continue;
            $existing_photos[] = $id;
            // Повторно не будет расшарена
            if(Sharing::where('user_id', $user->id)->where('photo_id', $photo->id)->first()) continue;
            // Шаринг
            $share = new Sharing();
            $share->photo_id = $photo->id;
            $share->user_id = $user->id;
            $share->save();
        }
        
        return response()->json(["existing_photos" => $existing_photos], 201);
        
    }

}
