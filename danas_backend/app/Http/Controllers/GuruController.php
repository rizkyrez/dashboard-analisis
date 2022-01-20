<?php

namespace App\Http\Controllers;

use DeepCopy\f001\A;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class GuruController extends Controller
{
    public function FailureReport () {
        $akad = DB::connection('mysqlakad');

        $fail_report = $akad->select();

        $fail_report =  $this->changeToArr($fail_report);

        $arrdata = [];
        foreach($fail_report as $flr) {
            $arrdata[] = array('value' => $flr['total'],'dep' => $flr['dep'],'group' => $flr['satker']);
        }

        return response()->json([
            'dataset' => $arrdata
        ]);
    }

    public function administrativeMonitor (Request $request) { 
        
        $akad = DB::connection('mysqlakad');
        $filter_departemen = $request->input('departemen');
        
        $monitoring_data = $akad->table('nilaiujian as nu')
        ->select(DB::raw('AVG(DATEDIFF(nu.ts, u.tanggal)), MAX(DATEDIFF(nu.ts, u.tanggal)), MIN(DATEDIFF(nu.ts, u.tanggal)), an.nipguru, t.departemen, ta.replid idtahunajaran, ta.tahunajaran'))
        ->join('ujian as u', 'u.replid', '=', 'nu.idujian')
        ->join('kelas as k', 'k.replid', '=', 'u.idkelas')
        ->join('aturannhb as an', 'an.replid', '=', 'u.idaturan')
        ->join('tingkat as t', 't.replid', '=', 'an.idtingkat')
        ->join('tahunajaran as ta', 'ta.replid', '=', 'k.idtahunajaran')
        ->where('ta.aktif', true)
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('t.departemen', $filter_departemen);
        })
        ->groupBy('ta.replid','an.nipguru','t.departemen','ta.tahunajaran')
        ->get();

        return response()->json([
            'dataset' => []
        ],200);

    }

    protected function searchArrId($field, $value, $array) {
        foreach ($array as $val) {
            if ($val[$field] === $value) {
                return $val;
            }
        }
        return null;
    }

    protected function changeToArr($arr) {
        // change to array  
        $rconvert = array_map(function($arrdp){
            return (array) $arrdp;
        }, $arr);

        return $rconvert;
    }
}

