<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CalonSiswaController extends Controller
{
    public function trendcs() {
        $akad =  DB::connection('mysqlakad');
        
        $trendcs = $akad->select('select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs left join prosespenerimaansiswa as pps on cs.idproses = pps.replid group by pps.replid, pps.departemen');
   
        // change to array 
        $trendcs = array_map(function($arrcs){
            return (array) $arrcs;
        }, $trendcs);

        // grouping by proses
        $array_group = [];
        foreach ($trendcs as $key => $item) {
            $array_group[$item['proses']][$key] =$item;
        }
     
        $departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        // change to array
        $departemen = array_map(function($arrdp){
            return (array) $arrdp;
        }, $departemen);

        $array_departemen = [];
        foreach ($departemen as $keyd => $arr_dep) {
            $array_departemen[] = $arr_dep['departemen'];
        }

        $arraydata = [];
        $array_proses = [];
        foreach ($array_group as $key => $arr_proses) {
            $array_detail = array();
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_proses);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               $array_detail[] = array($dep['departemen'],$total);
               // print_r($src_data);
               // print_r('<br/>');
            }
            $array_proses[] = $key;
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;   
        }
        // print_r($array_departemen);
        // print_r('<br/>');
        // die;

        return response()->json([
            'data_table' => $trendcs,
            'data_departemen' => $array_departemen,
            'data_proses' => $array_proses,
            'dataset' => $arraydata
        ]);
    }

    // protected function filter($key, $value) {

    // }

    protected function searchArrId($field, $value, $array) {
        foreach ($array as $val) {
            if ($val[$field] === $value) {
                return $val;
            }
        }
        return null;
     }
}
