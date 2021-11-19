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
        $umum = DB::connection('mysqlumum');

        // main query
        $agama = $akad->select("select count(cs.replid) as total, ag.agama from jbsakad.calonsiswa as cs inner join jbsakad.prosespenerimaansiswa as pps on cs.idproses = pps.replid right join jbsumum.agama as ag on cs.agama = ag.agama group by ag.agama");

        $agama = $this->changeToArr($agama);

        // query list option proses
        $proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps inner join calonsiswa as cs on pps.replid = cs.idproses group by pps.replid,pps.departemen" );

        // query list option departemen
        $list_departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        // query list agama
        $list_agama = $umum->select("select agama from agama order by urutan asc");

        $array_departemen = ['All'];
        $array_proses = ['All'];
        $array_agama = ['All'];

        foreach($proses as $prs) {
            $array_proses[] = $prs->proses;
        }
        
        foreach($list_departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        foreach($list_agama as $agm) {
            $array_agama[] = $agm->agama;
        }

        $arragdata = [];
        foreach ($agama as $agd) {
            $arragdata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'agdata' => $arragdata,
            'list_proses' => $array_proses,
            'list_departemen' => $array_departemen,
            'list_agama' => $array_agama
        ]);
    }

    public function filterag(Request $request) {
        $akad = DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_agama =    ($request->agama) ? "ag.agama ='".$request->agama."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        $condition2 = (($filter_departemen && $filter_agama && $filter_proses) && ($filter_proses != "")) ? " and ".$filter_agama: $filter_agama;

        // where clause trendcs
        $clause_agama = ($filter_departemen || $filter_proses || $filter_agama) ? "where ".$filter_departemen.$condition.$filter_proses.$condition2 : "";

        $main_query = "select count(cs.replid) as total, ag.agama from jbsakad.calonsiswa as cs inner join jbsakad.prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join jbsumum.agama as ag on cs.agama = ag.agama $clause_agama group by ag.agama";

        // main query
        $agama = $akad->select($main_query);

        $agama = $this->changeToArr($agama);

        $arragdata = [];
        foreach ($agama as $agd) {
            $arragdata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'agdata' => $arragdata
        ]);
    }

    

    public function exin_asalsekolah() {
        $akad = DB::connection('mysqlakad');

        // main query
        $asalsekolah = $akad->select("select count(cs.replid) as total, ak.sekolah, ak.departemen from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join asalsekolah as ak on cs.asalsekolah = ak.sekolah group by ak.sekolah");

        $asalsekolah = $this->changeToArr($asalsekolah);

        // query list option proses
        $proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps inner join calonsiswa as cs on pps.replid = cs.idproses group by pps.replid,pps.departemen" );

        // query list option departemen
        $list_departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        // query list agama
        $list_asalsekolah = $akad->select("select sekolah from asalsekolah order by departemen");

        $array_departemen = ['All'];
        $array_proses = ['All'];
        $array_asalsekolah = ['All'];

        foreach($proses as $prs) {
            $array_proses[] = $prs->proses;
        }
        
        foreach($list_departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        foreach($list_asalsekolah as $ask) {
            $array_asalsekolah[] = $ask->sekolah;
        }

        $arraskdata = [];
        foreach ($asalsekolah as $ask) {
            $arraskdata[] = array('value' => $ask['total'], 'name' => $ask['sekolah']);
        }

        return response()->json([
            'askdata' => $arraskdata,
            'list_proses' => $array_proses,
            'list_departemen' => $array_departemen,
            'list_asalsekolah' => $array_asalsekolah
        ]);
        
    }

    public function filterask(Request $request) { 
        $akad = DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        // where clause trendcs
        $clause_asalsekolah = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen.$condition.$filter_proses : "";

        // main query
        $main_query = "select count(cs.replid) as total, ak.sekolah, ak.departemen from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join asalsekolah as ak on cs.asalsekolah = ak.sekolah $clause_asalsekolah group by ak.sekolah";
        $asalsekolah = $akad->select($main_query);

        $asalsekolah = $this->changeToArr($asalsekolah);

        $arraskdata = [];
        foreach ($asalsekolah as $ask) {
            $arraskdata[] = array('value' => $ask['total'], 'name' => $ask['sekolah']);
        }

        return response()->json([
            'askdata' => $arraskdata,
        ]);
    }

    public function listFilter(Request $request) {
        $akad =  DB::connection('mysqlakad');
        $umum = DB::connection('mysqlumum');  

        // query list option departemen
        $departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        // query list option proses
        $list_proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps right join departemen as dp on pps.departemen = dp.departemen group by pps.replid, dp.departemen" );

        $list_agama = $umum->select('select agama from agama order by uturan asc');
        
        return response()->json([
            'list_departemen' => $departemen,
            'list_agama' => $list_agama,
            'list_proses' => $list_proses
        ]);
    }

    public function filterse(Request $request) {
        $akad = DB::connection('mysqlakad');
        $umum = DB::connection('mysqlumum');
    }

    // public function area() {

    // }

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

    // protected function gendataset() { 

    // }
}
