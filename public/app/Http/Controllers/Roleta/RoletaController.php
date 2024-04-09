<?php

namespace App\Http\Controllers\Roleta;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Roleta;
use App\Models\User;
use Carbon\Carbon;
use Auth;

class RoletaController extends Controller
{
    public function index($id){

        $user = User::findOrFail($id);

        $roleta = Roleta::where('user_id' ,$id)->get();

        return view('Roleta.Index',[
            'user' => $user,
            'roleta' => $roleta
        ]);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'item' => ['required', 'min:3'],
            'cor' => ['required'],
            ]);
    }

    public function adicionarRoleta($id){

         $roleta = Roleta::find($id);

        $array = [
            'item' => 'Sem Sorte',
            'cor' => '#444',
            'user_id' => $id
        ];

        if(!$roleta){
            for($i = 1; $i <= 8; $i++){
                $roul = new Roleta($array);
                $roul->save();
            }
        }

        return redirect()->back();


    }

    public function update(Request $request){

        $services = Roleta::findOrFail($request->id);

        $validator = $this->validator($request->all());
         if ($validator->fails()) {
             return response()->json([
                 'success' => 'false',
                 'errors'  => $validator->errors()->all(),
             ], 400);
         }else{
             $update =  Roleta::where('id', $request->id)->update(['item' => $request->item, 'cor' => $request->cor]);
            if($update){
                return response()->json(['success' => true], 200);
            }else{
                return response()->json(['success' => false], 400);
            }

         }

    }

    public function status(Request $request){

        $update =  Roleta::where('id', $request->id)->update(['status' => $request->status]);

        if($update){
            return response()->json(['success' => true], 200);
        }else{
            return response()->json(['success' => false], 400);
        }

    }

    public function return(Request $request){

    $item = Roleta::findOrFail($request->id);

        $array = [
            'item' => $item->item,
            'cor' => $item->cor
        ];

        echo json_encode($array);

        exit;


    }
}
