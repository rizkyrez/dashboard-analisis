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


        $array_table = [];
        $arraydata = [];
        $total_by_proses = [];
        $total_by_departemen = [];
        $arr_headpros = [];

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
            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail, 'animationDurationUpdate' => 500, 'universalTransition' => array('enabled' =>  true, 'divideShape' =>'clone'));
            $arraydata[] = $array_series;   
        }
        // print_r($total_by_proses);
        // print_r('<br/>');die;

        // array for table
        foreach ($departemen as $prodep) {
            $array_detail_table = [];
            $array_prs = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('departemen', $prodep['departemen'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
                $array_prs[] = $key;
            }

            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'],'proses'=> $array_prs, 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }

        // print_r($total_by_departemen);die;

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_pros' => $total_by_proses,
            'header' => $arr_headpros
        ],200);
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
        ],200);
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
     
        // query main departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen $clause_departemen order by urutan asc");

        // change to array  
        $departemen = $this->changeToArr($departemen);

        $array_table = [];
        $arraydata = [];
        $total_by_proses = [];
        $total_by_departemen = [];

        $arr_headpros = [];
        // array series chart
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
            $arraydata[] = array('name' => $key, 'type' => 'bar','data' => $array_detail, 'animationDurationUpdate' => 500, 'universalTransition' => array('enabled' =>  true, 'divideShape' =>'clone')); 
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
        $agama = $akad->select("select count(cs.replid) as total, (CASE WHEN cs.agama is null THEN 'NULL' ELSE ag.agama END) as agama from jbsakad.calonsiswa as cs inner join jbsakad.prosespenerimaansiswa as pps on cs.idproses = pps.replid left join jbsumum.agama as ag on cs.agama = ag.agama group by ag.agama");

        $agama = $this->changeToArr($agama);

        $arragdata = [];
        foreach ($agama as $agd) {
            $arragdata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }

        return response()->json([
            'agdata' => $arragdata
        ]);
    }

    public function filterag(Request $request) {
        $akad = DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_agama =    ($request->agama) ? "ag.agama ='".$request->agama."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        if($request->agama != "") { 
            $condition2 = ($filter_departemen == "" && $filter_proses == "") ? "" : " and ";
        } 

        $query_filter = (isset($condition2)) ? $filter_departemen.$condition.$filter_proses.$condition2.$filter_agama : $filter_departemen.$condition.$filter_proses;

        // where clause trendcs
        $clause_agama = ($filter_departemen || $filter_proses ) ? "where ".$query_filter : "";

        $main_query = "select count(cs.replid) as total, (CASE WHEN ag.agama is null THEN 'NULL' ELSE ag.agama END) as agama from jbsakad.calonsiswa as cs inner join jbsakad.prosespenerimaansiswa as pps on cs.idproses = pps.replid left join jbsumum.agama as ag on cs.agama = ag.agama $clause_agama group by ag.agama";

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
        $asalsekolah = $akad->select("select count(cs.replid) as total, (CASE WHEN cs.asalsekolah is null THEN 'NULL' ELSE ak.sekolah END) as sekolah, ak.departemen from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid left join asalsekolah as ak on cs.asalsekolah = ak.sekolah group by ak.sekolah");

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
        $arrdataseries = [];
        foreach ($asalsekolah as $ask) {
            $arraskdata[] = array('value' => $ask['total'], 'name' => $ask['sekolah']);
            $arrdataseries[] = array('name' => $ask['sekolah'], 'type' => 'bar', 'data' => $ask['total']);
        }

        return response()->json([
            'askdata' => $arraskdata,
            'dataseries' => $arrdataseries,
            'list_proses' => $array_proses,
            'list_departemen' => $array_departemen,
            'list_asalsekolah' => $array_asalsekolah
        ]);
        
    }

    public function filterask(Request $request) { 
        $akad = DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "ak.departemen ='".$request->departmen."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        // where clause trendcs
        $clause_asalsekolah = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen.$condition.$filter_proses : "";

        // main query
        $main_query = "select count(cs.replid) as total, (CASE WHEN cs.asalsekolah is null THEN 'NULL' ELSE ak.sekolah END) as sekolah, ak.departemen from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid left join asalsekolah as ak on cs.asalsekolah = ak.sekolah $clause_asalsekolah group by ak.sekolah";
        
        $asalsekolah = $akad->select($main_query);

        $asalsekolah = $this->changeToArr($asalsekolah);

        $arraskdata = [];
        $arrdataseries = [];
        foreach ($asalsekolah as $ask) {
            $arraskdata[] = array('value' => $ask['total'], 'name' => $ask['sekolah']);
            $arrdataseries[] = array('name' => $ask['sekolah'], 'type' => 'bar', 'data' => $ask['total']);
        }

        return response()->json([
            'askdata' => $arraskdata,
            'dataseries' => $arrdataseries,
        ]);
    }

    public function achievement() {
        $akad = DB::connection('mysqlakad');

        $query_active_proses = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid where pps.aktif = 1 group by pps.replid, pps.departemen";

        $active_proses = $akad->select($query_active_proses);
        $active_proses = $this->changeToArr($active_proses);

        $query_last_proses = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid where pps.aktif != 1 and ta.aktif = 1 and ta.departemen = pps.departemen group by pps.replid, pps.departemen order by pps.replid desc";
        
        $last_proses = $akad->select($query_last_proses);
        $last_proses = $this->changeToArr($last_proses);

        $count_active = [];
        $count_last = [];
        $proses_last = '';
        $proses_active = '';

        foreach ($active_proses as $key => $item) {
            $proses_active = $item['proses'];
            $count_active[] = $item['total'];
        }
        foreach ($last_proses as $key => $item) {
            $proses_last = $item['proses'];
            $count_last[] = $item['total'];
        } 

        $total_active = array_sum($count_active);
        $total_last = array_sum($count_last);

        $arraydata = array($total_last, $total_active);
        $arrayproses = array($proses_last, $proses_active);
        
        return response()->json([
            'data_table' => $arraydata,
            'header' => $arrayproses,
        ],200);
    }

    public function filterAchv(Request $request) {
        $akad = DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_proses = ($request->proses) ? "pps.proses ='".$request->proses."'" : "";
        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        // where clause trendcs
        $clause_achievement = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen.$condition.$filter_proses : "";

        $query_active_proses = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid $clause_achievement group by pps.replid, pps.departemen";

        $active_proses = $akad->select($query_active_proses);
        $active_proses = $this->changeToArr($active_proses);

        $query_last_proses = "select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid $clause_achievement and ta.departemen = pps.departemen group by pps.replid, pps.departemen order by pps.replid desc";
        
        $last_proses = $akad->select($query_last_proses);
        $last_proses = $this->changeToArr($last_proses);

        $count_active = [];
        $count_last = [];
        $proses_active = '';
        $proses_last = '';

        foreach ($active_proses as $item) {
            $proses_active = $item['proses'];
            $count_active[] = $item['total'];
        }
        foreach ($last_proses as $item) {
            $proses_last = $item['proses'];
            $count_last[] = $item['total'];
        } 

        $total_active = array_sum($count_active);
        $total_last = array_sum($count_last);

        $arraydata = array($total_last, $total_active);
        $arrayproses = array($proses_last, $proses_active);
        
        return response()->json([
            'data_table' => $arraydata,
            'header' => $arrayproses,
        ],200);
    }

    public function CsbAchievement() {
        $akad = DB::connection('mysqlakad');

        $csbsibling = $akad->select("SELECT COUNT(s.replid) AS total, dp.departemen FROM siswa AS s INNER JOIN kelas AS k ON s.idkelas = k.replid INNER JOIN tahunajaran as ta ON k.idtahunajaran = ta.replid INNER JOIN tingkat AS tk ON k.idtingkat = tk.replid INNER JOIN departemen AS dp ON ta.departemen = dp.departemen LEFT JOIN calonsiswa AS cs ON cs.kk_no = s.kk_no JOIN (SELECT MAX(t.tingkat) AS last_tingkat,t.departemen as dpq FROM `tingkat` as t INNER JOIN departemen as dp ON t.departemen = dp.departemen GROUP BY dp.departemen) as q2 WHERE dp.departemen = q2.dpq AND tk.tingkat = q2.last_tingkat AND cs.kk_no IS NOT NULL AND cs.kk_no <> '' AND s.kk_no IS NOT NULL AND s.kk_no <> '' GROUP BY q2.dpq");

        $csbsibling = $this->changeToArr($csbsibling);

        $achievement = $akad->select("SELECT COUNT(cs.replid) AS total, pps.departemen FROM calonsiswa AS cs INNER JOIN prosespenerimaansiswa AS pps ON cs.idproses = pps.replid WHERE cs.asalsekolah LIKE '%narada%' GROUP BY pps.departemen");

        $achievement = $this->changeToArr($achievement);

        $arr_csb_sibling = [];
        $arr_achievement_internal = [];

        foreach ($csbsibling as $item) {
            $arr_csb_sibling[] = $item['total'];
        }
        foreach ($achievement as $item) {
            $arr_achievement_internal[] = $item['total'];
        } 

        $total_csbpot = array_sum($arr_csb_sibling);
        $total_ach = array_sum($arr_achievement_internal);

        $arraydata = array($total_csbpot,$total_ach);

        return response()->json([
            'data_table' => $arraydata
        ],200);
    }


    public function FilterCsb(Request $request) {
        $akad = DB::connection('mysqlakad');

        $filter_departemen = ($request->departmen) ? "pps.departemen = '".$request->departemen."'" : "" ;
        $filter_proses = ($request->proses) ? "pps.proses = '".$request->proses."'" : "";
        $filter_sibling = ($request->sibling != 0) ? "LEFT JOIN calonsiswa AS cs ON cs.kk_no = s.kk_no" : "";

        $condition = ($filter_departemen && $filter_proses) ? " and " : "";

        $clause_csbpot = ($filter_departemen || $filter_proses) ? "where ".$filter_departemen.$condition.$filter_proses : "";

        $csbsibling = $akad->select("SELECT COUNT(s.replid) AS total, dp.departemen FROM siswa AS s INNER JOIN kelas AS k ON s.idkelas = k.replid INNER JOIN tahunajaran as ta ON k.idtahunajaran = ta.replid INNER JOIN tingkat AS tk ON k.idtingkat = tk.replid INNER JOIN departemen AS dp ON ta.departemen = dp.departemen $filter_sibling JOIN (SELECT MAX(t.tingkat) AS last_tingkat,t.departemen as dpq FROM `tingkat` as t INNER JOIN departemen as dp ON t.departemen = dp.departemen GROUP BY dp.departemen) as q2 WHERE dp.departemen = q2.dpq AND tk.tingkat = q2.last_tingkat AND cs.kk_no IS NOT NULL AND cs.kk_no <> '' AND s.kk_no IS NOT NULL AND s.kk_no <> '' GROUP BY q2.dpq");

        $csbsibling = $this->changeToArr($csbsibling);

        $achievement = $akad->select("SELECT COUNT(cs.replid) AS total, pps.departemen FROM calonsiswa AS cs INNER JOIN prosespenerimaansiswa AS pps ON cs.idproses = pps.replid WHERE cs.asalsekolah LIKE '%narada%' GROUP BY pps.departemen");

        $achievement = $this->changeToArr($achievement);

        $arraydata = [];

        return response()->json([
            'data_table' => $arraydata
        ]);
    }

    public function ListFilter() {
        $akad = DB::connection('mysqlakad');
        $umum = DB::connection('mysqlumum');  

        // query list option departemen
        $departemen = $akad->select('select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc');

        // query list option proses
        $list_proses = $akad->select("select distinct pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps inner join departemen as dp on pps.departemen = dp.departemen group by pps.replid, dp.departemen");

        $list_agama = $umum->select('select agama from agama order by urutan asc');

        $array_departemen = ['All'];
        $array_proses = ['All'];
        $array_agama = ['All'];

        foreach($list_proses as $prs) {
            $array_proses[] = $prs->proses;
        }
        
        foreach($departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        foreach($list_agama as $agm) {
            $array_agama[] = $agm->agama;
        }

        return response()->json([
            'list_departemen' => $array_departemen,
            'list_agama' => $array_agama,
            'list_proses' => $array_proses
        ]);
    }

    // public function FilterCsbAch() {
    //     $akad = DB::connection('mysqlakad');

    //     $filter_tingkat = ;
    //     $filter_departemen = ;

    //     return response()->json([
            
    //     ])
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
        $rconvert = array_map(function($arrpar){
            return (array) $arrpar;
        }, $arr);

        return $rconvert;
    }

    // protected function gendataset() { 

    // }
}
