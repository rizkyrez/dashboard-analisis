<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CalonSiswaController extends Controller
{
    public function trendcs() {
        $akad =  DB::connection('mysqlakad');
        
        // main query
        $trendcs = $akad->select("select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid group by pps.replid, pps.departemen");
   
        // change to array 
        $trendcs = $this->changeToArr($trendcs);

        // grouping by proses
        $array_group = [];
        foreach ($trendcs as $key => $item) {
            $array_group[$item['proses']][$key] =$item;
        }
     
        // query main departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc");
     
        // change to array  
        $departemen = $this->changeToArr($departemen);

        // query list option proses
        $proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps inner join calonsiswa as cs on pps.replid = cs.idproses group by pps.replid,pps.departemen" );

        // query list option departemen
        $list_departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        $array_table = [];
        $arraydata = [];
        $array_departemen = ['All'];
        $total_by_proses = [];
        $total_by_departemen = [];
        $arr_headpros = [];

        $array_proses = ['All'];
        foreach($proses as $prs) {
            $array_proses[] = $prs->proses;
        }
        
        foreach($list_departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        // array for series chart
        foreach ($array_group as $key => $arr_proses) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_proses);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               $array_detail[] = array($dep['departemen'],$total);
               $arr_total[]  = $total;
        
            }
            $sum = array_sum($arr_total);
            $total_by_proses[] = $sum;

            $arr_headpros[] = $key;
            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;   
        }
        // print_r($total_by_proses);
        // print_r('<br/>');die;

        // array for table
        foreach ($departemen as $prodep) {
            $array_detail_table = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('departemen', $prodep['departemen'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
                
            }

            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'],'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }

        // print_r($total_by_departemen);die;

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'list_proses' => $array_proses,
            'list_departemen' => $array_departemen,
            'sum_pros' => $total_by_proses,
            'header' => $arr_headpros
        ]);
    }

    public function change(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_proses = ($request->departmen) ? "where pps.departemen ='".$request->departmen."'" : "";

        // query list option proses
        $proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps inner join calonsiswa as cs on pps.replid = cs.idproses $filter_proses group by pps.replid,pps.departemen");

        $array_proses = [];
        $array_proses[] = 'All';
        foreach($proses as $prs) {
            $array_proses[] = $prs->proses;
        }

        return response()->json([
            'list_proses' => $array_proses,
        ]);
    }

    public function filter(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        // where clause trendcs
        $clause_trendcs = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen.$condition.$filter_proses : "";

        // main query
        $main_query = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid $clause_trendcs group by pps.replid, pps.departemen";

        $trendcs = $akad->select($main_query);

        // change to array 
        $trendcs = $this->changeToArr($trendcs);
        
        // grouping by proses
        $array_group = [];
        foreach ($trendcs as $key => $item) {
            $array_group[$item['proses']][$key] =$item;
        }

        //  where clause departemen 
        $clause_departemen = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen : "";

        $second_query = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid $clause_departemen group by pps.replid, pps.departemen";

        $trendcs2ds = $akad->select($second_query);

        // change to array 
        $trendcs2ds = $this->changeToArr($trendcs2ds);
        
        // grouping by proses
        $array_group2 = [];
        foreach ($trendcs2ds as $key => $item) {
            $array_group2[$item['proses']][$key] =$item;
        }
     
        // query main departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen $clause_departemen order by urutan asc");

        // change to array  
        $departemen = $this->changeToArr($departemen);

        $array_table = [];
        $arraydata = [];
        $total_by_proses = [];
        $total_by_departemen = [];

        // array for series chart
        foreach ($array_group2 as $key => $arr_proses) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_proses);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               $array_detail[] = array($dep['departemen'],$total);

               $arr_x[] = $dep['departemen'];
        
            }

            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;   
        }

        $arr_headpros = [];
        // array total
        foreach ($array_group as $key => $arr_proses) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_proses);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               
               $arr_total[]  = $total;
        
            }
            $sum = array_sum($arr_total);
            $total_by_proses[] = $sum;
            $arr_headpros[] = $key;
        }

        // array for table
        foreach ($departemen as $prodep) {
            $array_detail_table = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('departemen', $prodep['departemen'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
                
            }
            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'],'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }
        // print_r($total_by_departemen);die;

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_pros' => $total_by_proses,
            'header' => $arr_headpros
        ]);
    }

    public function agama() {
        $akad = DB::connection('mysqlakad');

        // main query
        $agama = $akad->select("select count(cs.replid) as total, ag.agama from jbsakad.calonsiswa as cs inner join jbsakad.prosespenerimaansiswa as pps on cs.idproses = pps.replid right join jbsumum.agama as ag on cs.agama = ag.agama group by ag.agama");

        $agama = $this->changeToArr($agama);

        $arragdata = [];
        foreach ($agama as $agd) {
            $arragdata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'agdata' => $arragdata
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
