<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function trendsiswa() {
        $akad = DB::connection('mysqlakad');

        $trend_siswa = $akad->select('select count(s.replid) as total, ta.tahunajaran as tahunajaran, dp.departemen as departemen from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join departemen as dp on ta.departemen = dp.departemen group by ta.replid, dp.departemen');
        
        // change to array 
        $trend_siswa = $this->changeToArr($trend_siswa);

        // grouping by proses
        $array_group = [];
        foreach ($trend_siswa as $key => $item) {
            $array_group[$item['tahunajaran']][$key] =$item;
        }
     
        // query main departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen inner join kelas as k on k.idtahunajaran = ta.replid order by urutan asc");
     
        // change to array  
        $departemen = $this->changeToArr($departemen);

        $array_table = [];
        $arraydata = [];
        $total_by_tahunajaran = [];
        $total_by_departemen = [];
        $arr_headerta = [];
        
        // array for series chart
        foreach ($array_group as $key => $arr_tahunajaran) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_tahunajaran);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               $array_detail[] = array($dep['departemen'],$total);
               $arr_total[]  = $total;
        
            }
            $sum = array_sum($arr_total);
            $total_by_tahunajaran[] = $sum;

            $arr_headerta[] = $key;
            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;   
        }
        // print_r($total_by_proses);
        // print_r('<br/>');die;

        // array for table
        foreach ($departemen as $prodep) {
            $array_detail_table = [];
            $header = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('departemen', $prodep['departemen'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
                $header[] = $key;
            }

            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'],'header' => $header, 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_pros' => $total_by_tahunajaran,
            // 'header' => $arr_headerta
        ],200);
    }

    public function ListFilterSiswa() {
        $akad = DB::connection('mysqlakad');
        $umum = DB::connection('mysqlumum');  

        // query list option departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen inner join kelas as k on k.idtahunajaran = ta.replid order by urutan asc");

        // query list option proses
        $list_tahunajaran = $akad->select("select distinct ta.tahunajaran as tahunajaran from tahunajaran as ta inner join kelas as k on k.idtahunajaran = ta.replid inner join siswa as s on s.idkelas = k.replid group by ta.replid,ta.departemen");

        $list_agama = $umum->select('select agama from agama order by urutan asc');

        $array_departemen = ['All'];
        $array_tahunajaran = ['All'];
        $array_agama = ['All'];

        foreach($list_tahunajaran as $thj) {
            $array_tahunajaran[] = $thj->tahunajaran;
        }
        
        foreach($departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        foreach($list_agama as $agm) {
            $array_agama[] = $agm->agama;
        }

        return response()->json([
            'list_dp' => $array_departemen,
            'list_ag' => $array_agama,
            'list_tahunajaran' => $array_tahunajaran
        ]);
    }

    public function agama() {
        $akad = DB::connection('mysqlakad');

        // main query
        $agama = $akad->select("select count(s.replid) as total, ag.agama from jbsakad.siswa as s inner join jbsakad.kelas as k on s.idkelas = k.replid inner join jbsakad.tahunajaran as ta on k.idtahunajaran = ta.replid right join jbsumum.agama as ag on s.agama = ag.agama group by ag.agama");

        $agama = $this->changeToArr($agama);

        $arraydata = [];
        foreach ($agama as $agd) {
            $arraydata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function change(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_tahunajaran = ($request->departmen) ? "where ta.departemen ='".$request->departmen."'" : "";

        // query list option tahunajaran
        $tahunajaran = $akad->select("select distinct ta.tahunajaran as tahunajaran from tahunajaran as ta inner join kelas as k on k.idtahunajaran = ta.replid inner join siswa as s on s.idkelas = k.replid $filter_tahunajaran group by ta.replid,ta.departemen");

        $array_tahunajaran = ['All'];
        foreach($tahunajaran as $thj) {
            $array_tahunajaran[] = $thj->tahunajaran;
        }

        return response()->json([
            'list_tahunajaran' => $array_tahunajaran,
        ],200);
    }

    public function FilterTrend(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "ta.departemen ='".$request->departmen."'" : "";
        $filter_tahunajaran = ($request->tahunajaran) ? "ta.tahunajaran ='".$request->tahunajaran."'" : "";
        $condition = ($filter_departemen && $filter_tahunajaran) ? " and " : "";

        // where clause trendcs
        $clause_trend_siswa = ($filter_departemen || $filter_tahunajaran) ? "where ".$filter_departemen.$condition.$filter_tahunajaran : "";

        // trend query
        $main_query = "select count(s.replid) as total, ta.tahunajaran as tahunajaran, dp.departemen as departemen from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join departemen as dp on ta.departemen = dp.departemen $clause_trend_siswa group by ta.replid, dp.departemen";

        $trend_siswa = $akad->select($main_query);

        // change to array 
        $trend_siswa = $this->changeToArr($trend_siswa);
        
        // grouping by proses
        $array_group = [];
        foreach ($trend_siswa as $key => $item) {
            $array_group[$item['tahunajaran']][$key] =$item;
        }

        //  where clause departemen 
        $clause_departemen = ($filter_departemen || $filter_tahunajaran) ? "where ".$filter_departemen : "";

        $second_query = "select count(s.replid) as total, ta.tahunajaran as tahunajaran, dp.departemen as departemen from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join departemen as dp on ta.departemen = dp.departemen $clause_departemen group by ta.replid, dp.departemen";

        $trend_siswa2ds = $akad->select($second_query);

        // change to array 
        $trend_siswa2ds = $this->changeToArr($trend_siswa2ds);
        
        // grouping by proses
        $array_group2 = [];
        foreach ($trend_siswa2ds as $key => $item) {
            $array_group2[$item['tahunajaran']][$key] =$item;
        }
     
        // query main departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen inner join kelas as k on k.idtahunajaran = ta.replid $clause_departemen order by urutan asc");
     
        // change to array  
        $departemen = $this->changeToArr($departemen);

        $array_table = [];
        $arraydata = [];
        $total_by_tahunajaran = [];
        $total_by_departemen = [];

        // array for series chart
        foreach ($array_group2 as $key => $arr_tahunajaran) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_tahunajaran);
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

        $arr_headerta = [];
        // array total
        foreach ($array_group as $key => $arr_tahunajaran) {
            $array_detail = array();
            $arr_total = [];
            foreach ($departemen as $dep) {
               $src_data = $this->searchArrId('departemen', $dep['departemen'], $arr_tahunajaran);
               $total = $src_data['total'];
               if ($src_data == null) {
                    $total = 0;
               }
               
               $arr_total[]  = $total;
        
            }
            $sum = array_sum($arr_total);
            $total_by_tahunajaran[] = $sum;
            $arr_headerta[] = $key;
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
                $header[] = $key;
            }
            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'], 'header' => $header, 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }
        
        // print_r($total_by_departemen);die;

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata, 
            'sum_pros' => $total_by_tahunajaran,
            'header' => $arr_headerta
        ]);
    }

    public function FilterAg(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "ta.departemen ='".$request->departmen."'" : "";
        $filter_tahunajaran = ($request->tahunajaran) ? "ta.tahunajaran ='".$request->tahunajaran."'" : "";
        $filter_agama = ($request->agama)? "ag.agama='".$request->agama."'" : "";
        $condition = ($filter_departemen && $filter_tahunajaran) ? " and " : "";

        if($request->agama != "") { 
            $condition2 = ($filter_departemen == "" && $filter_tahunajaran == "") ? "" : " and ";
        } 

        $query_filter = (isset($condition2)) ? $filter_departemen.$condition.$filter_tahunajaran.$condition2.$filter_agama : $filter_departemen.$condition.$filter_tahunajaran;
        $clause_agama_siswa = ($filter_departemen || $filter_tahunajaran) ? "where ".$query_filter : "";

        $query_agama = "select count(s.replid) as total, ag.agama from jbsakad.siswa as s inner join jbsakad.kelas as k on s.idkelas = k.replid inner join jbsakad.tahunajaran as ta on k.idtahunajaran = ta.replid inner join jbsumum.agama as ag on s.agama = ag.agama $clause_agama_siswa group by ag.agama";

        // main query
        $agama = $akad->select($query_agama);

        $agama = $this->changeToArr($agama);

        $arraydata = [];
        foreach ($agama as $agd) {
            $arraydata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'dataset' => $arraydata
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
