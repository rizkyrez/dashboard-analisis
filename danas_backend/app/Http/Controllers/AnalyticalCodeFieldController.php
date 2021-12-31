<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnalyticalCodeFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $analytical_code_list = DB::connection('mysqlakad')->table('analytical_code_field')->orderByRaw('id DESC')->get();    
        return response()->json([
            'success' => true,
            'message' => 'List Data',
            'data' => $analytical_code_list,
        ], 200);
    }


    /** 
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

		$validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);
        
        //response error validation
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Kelas Failed to Save', 'errorValidations'=>$validator->errors()], 201);
        }

		$created = date("Y-m-d H:i:s");
        
        $kelas = DB::connection('mysqlakad')->table('analytical_code_field')->insert([
            'name' => $request->name,
            'data_type' => $request->data_type,
            'analytical_code_id' => $request->analytical_code_id,
            'date_created' => $created,
        ]);

		if($kelas){
			return response()->json([
				'success' => true,
				'message' => 'Class Created',
				'data'    => $kelas  
			], 201);
		}

		return response()->json([
            'success' => false,
            'message' => 'Failed to Create Class',
        ], 409);
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $kelas = DB::table('analytical_code_field')->where('id', $id)->get();
		$kelas = DB::connection('mysqlakad')->select("SELECT * FROM analytical_code_field WHERE id = '".$id."'");

		if($kelas){
			return response()->json([
				'success' => true,
				'message' => 'Detail Data Post',
				'data'    => $kelas[0]
			], 200);
		}
		return response()->json([
			'success' => false,
			'message' => 'Gagal Data Post',
			'data'    => $kelas 
		], 400);   
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

		//set validation
        $validator = Validator::make($request->all(), [
            'id'   => 'required',
            'name' => 'required',
        ]);
        
        //response error validation
        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Data Failed to Save', 'errorValidations'=>$validator->errors()], 201);
        }

		
		$updated = date("Y-m-d H:i:s A");

        $kelas = DB::connection('mysqlakad')->table('analytical_code_field')->where('id', '=', $request->id)->update([
            'name' => $request->name,
            'data_type' => $request->data_type,
            'analytical_code_id' => $request->analytical_code_id,
        ]);

        if($kelas) {

            return response()->json([
                'success' => true,
                'message' => 'Class Updated',
                'data'    => $kelas  
            ], 201);

        };
            return response()->json([
                'success' => false,
                'message' => 'Class Not Found => '.$kelas,
            ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $kelas = DB::connection('mysqlakad')->table('analytical_code_field')->where('id', '=',$id)->delete();

        if($kelas){
            return response()->json([
                'success' => true,
                'message' => 'Data Deleted',
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Data Not Found',
        ], 404);
    }
}
