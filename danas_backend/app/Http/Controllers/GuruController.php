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
