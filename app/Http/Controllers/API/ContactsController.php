<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ContactsController extends Controller
{
    public function index(Request $request){
        $contacts = DB::table('contacts')->get();
        $data = [];
        foreach($contacts as $item){
            $data[] = [
                'icon' => asset('uploads/system/' . $item->icon),
                'name' => $item->name,
                'value'=>$item->value,
                'type'=>$item->type
            ];
        }
        // dd($data);
        return response()->json([
            'code'=>0,
            'message'=>[],
            'response'=>$data
        ],200);
    }
}
