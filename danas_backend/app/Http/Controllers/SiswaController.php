<?php

namespace App\Http\Controllers;

use DeepCopy\f001\A;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class SiswaController extends Controller
{
    public function trendsiswa() {
        $akad = DB::connection('mysqlakad');

        $trend_siswa = $akad->select('select count(s.replid) as total, ta.tahunajaran as tahunajaran, dp.departemen as departemen from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join departemen as dp on ta.departemen = dp.departemen group by ta.replid, ta.departemen');
        
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
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('departemen', $prodep['departemen'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
            }

            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep['departemen'], 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_pros' => $total_by_tahunajaran,
            'header' => $arr_headerta
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

        $list_tingkat = $akad->select('select tingkat from tingkat order by urutan asc');

        $array_departemen = ['All'];
        $array_tahunajaran = ['All'];
        $array_agama = ['All'];
        $array_tingkat = ['All'];

        foreach($list_tahunajaran as $thj) {
            $array_tahunajaran[] = $thj->tahunajaran;
        }
        
        foreach($departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        foreach($list_agama as $agm) {
            $array_agama[] = $agm->agama;
        }

        foreach($list_tingkat as $tkt) {
            $array_tingkat[] = $tkt->tingkat;
        }

        return response()->json([
            'list_dp' => $array_departemen,
            'list_ag' => $array_agama,
            'list_tahunajaran' => $array_tahunajaran,
            'list_tingkat' => $array_tingkat
        ]);
    }

    public function ClassOcp() {
        $akad = DB::connection('mysqlakad');

        $main_query = "select count(s.replid) as total, k.kelas as kelas, t.tingkat as tingkat, dp.departemen as departemen, ta.tahunajaran from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join tingkat as t on k.idtingkat = t.replid inner join departemen as dp on t.departemen = dp.departemen where ta.aktif = 1 group by k.replid, t.tingkat order by k.replid asc";

        $class_occ = $akad->select($main_query);

        $class_occ = $this->changeToArr($class_occ);

        // query main departemen
        $tingkat = $akad->select("select distinct t.tingkat from tingkat as t inner join kelas as k on k.idtingkat = t.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid order by t.urutan asc");
     
        // change to array  
        $tingkat = $this->changeToArr($tingkat);

        $second_query = "select k.kapasitas, k.kelas as kelas, t.tingkat as tingkat, dp.departemen as departemen, ta.tahunajaran from siswa as s inner join kelas as k on s.idkelas = k.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid inner join tingkat as t on k.idtingkat = t.replid inner join departemen as dp on t.departemen = dp.departemen where ta.aktif = 1 group by k.replid, t.tingkat order by k.replid asc";

        $class_capacity = $akad->select($second_query);

        $class_capacity = $this->changeToArr($class_capacity);

        // $array_group = [];
        // $array_group2 = [];
        // foreach ($class_occ as $key => $item) {
        //     $array_group[$item['kelas']][$key] = $item;
        // }  

        // foreach ($class_capacity as $key => $item) {
        //     $array_group2[$item['kelas']][$key] = $item;
        // }  

        // print_r($tingkat);die;
        // print_r('<br>');
        // print_r($array_group);d

        $arr_data = [];
        $arr_data_cpt = [];
        foreach($class_occ as $class) { 
            $arr_tingkat = [];
            
            foreach ($tingkat as $tkt) {
                $arr_tingkat[] = $tkt['tingkat'];
            }

            $arr_data[] = $class['total'];
        }

        $arr_series[] =  array('name' => 'Terisi','type' => 'bar','data' => $arr_data, 'barGap' => '-100%','barCategoryGap' => '18%', 'label' => array('show' => true, 'position' => 'inside'), 'itemStyle' => array('color' => '#008f00'));

        $dat_kelas = [];
        foreach($class_capacity as $capacity) { 
            $arr_data_cpt[] = $capacity['kapasitas'];
            $dat_kelas[] = $capacity['kelas'];
        }

        $arr_series[] =  array('name' => 'Kapasitas','type' => 'bar','data' => $arr_data_cpt, 'barGap' => '-100%','barCategoryGap' => '18%', 'label' => array('show' => true,'rotate' => -90, 'position' => 'top','align' =>  'center',  'verticalAlign' => 'center','color' => '#000', 'fontSize' => '11', 'fontWeight' => 'bold', 'formatter' => "{b}  {c}", 'distance' => '32'), 'itemStyle' => array('color' => '#77aa77','opacity' => 0.5));

        return response()->json([
            'arraydata' => $arr_series,
            'tingkat' => $dat_kelas
        ]); 
    }

    public function agama() {
        $akad = DB::connection('mysqlakad');

        // main query
        $agama = $akad->select("select count(s.replid) as total, (CASE WHEN s.agama is null THEN 'NULL' ELSE ag.agama END) as agama from jbsakad.siswa as s inner join jbsakad.kelas as k on s.idkelas = k.replid inner join jbsakad.tahunajaran as ta on k.idtahunajaran = ta.replid inner join departemen as dp on ta.departemen = dp.departemen left join jbsumum.agama as ag on s.agama = ag.agama group by ag.agama");

        $agama = $this->changeToArr($agama);

        $arraydata = [];
        foreach ($agama as $agd) {
            $arraydata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function JmlKelasTingkat() {
        $akad = DB::connection('mysqlakad');

        $jml_kelas_ptingkat = $akad->select("select count(k.replid) as total, ta.tahunajaran as tahunajaran, t.tingkat as tingkat from kelas as k inner join tingkat as t on k.idtingkat = t.replid inner join tahunajaran as ta on k.idtahunajaran = ta.replid group by ta.replid, t.tingkat");

        $jml_kelas_ptingkat = $this->changeToArr($jml_kelas_ptingkat);

        // grouping by tahunajaran
        $array_group = [];
        foreach ($jml_kelas_ptingkat as $key => $item) {
            $array_group[$item['tahunajaran']][$key] =$item;
        }

        // find tingkat
        $tingkat = $akad->select("select distinct tingkat from tingkat as t inner join departemen as d on t.departemen = d.departemen inner join tahunajaran as ta on d.departemen = ta.departemen order by t.urutan asc");

        $tingkat = $this->changeToArr($tingkat);

        $array_table = [];
        $arraydata = [];    
        $total_by_tahunajaran = [];
        $total_by_tingkat = [];
        $arr_headerta = []; 

        foreach($array_group as $key => $arrth) {
            $array_detail = [];
            $arr_total = [];
            foreach($tingkat as $tkt) {
                $src_data = $this->searchArrId('tingkat', $tkt['tingkat'], $arrth);
                $total = $src_data['total'];
                if ($src_data == null) {
                    $total = 0;
                }
                $array_detail[] = $total;
                $arr_total[]  = $total;
            
            }
            $sum = array_sum($arr_total);
            $total_by_tahunajaran[] = $sum;

            $arr_headerta[] = $key; 
            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;

        }

        // array for table
        foreach ($tingkat as $tnkt) {
            $array_detail_table = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('tingkat', $tnkt['tingkat'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;    
                }
                $array_detail_table[] = $total2;
            }

            $total_by_tingkat = array_sum(array_values($array_detail_table));
            $array_table[] = array('tingkat' => $tnkt['tingkat'], 'total' => $array_detail_table,'grandtotal' => $total_by_tingkat);
            // print_r($total_by_departemen);
        }

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_thj' => $total_by_tahunajaran,
            'header' => $arr_headerta
        ],200);
    }

    public function siblings() {
        $akad = DB::connection('mysqlakad');
        
        $main_query = "SELECT COUNT(s.replid) as total, s.kk_no as kk_no, k.kelas as kelas,t.tingkat as tingkat, ta.departemen as departemen FROM siswa_saudara AS ss INNER JOIN siswa AS s ON ss.siswa_id = s.replid INNER JOIN kelas AS k ON k.replid = s.idkelas INNER JOIN tahunajaran AS ta ON ta.replid = k.idtahunajaran INNER JOIN tingkat as t ON k.idtingkat = t.replid WHERE (s.kk_no!='' OR s.kk_no!=null) AND s.alumni = 0 AND ta.aktif = 1 GROUP BY s.kk_no ORDER BY s.kk_no ASC";

        $siblings = $akad->select($main_query); 
        $siblings =  $this->changeToArr($siblings);
 
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen inner join kelas as k on k.idtahunajaran = ta.replid order by urutan asc");
        
        $departemen = $this->changeToArr($departemen);

        $arrgroup = [];
        foreach($siblings as $key => $sib) {
            $arrgroup[$sib['kk_no']][$key] = $sib;
        } 

        $arrsum = [];
        $arrdep = [];
        $arrdata = [];
        foreach($arrgroup as $key => $ssb) {
            $arrdetail = [];
            foreach($departemen as $dpt) {
                $src_data = $this->searchArrId('departemen', $dpt['departemen'], $ssb);
                if($src_data == null) {
                    $src_data['total'] = 0;
                }
                $arrdetail[] = $src_data['total'];
            }   
           
            foreach($ssb as $sb) {
                $kk = $sb['kk_no'];
                $tingkat = $sb['tingkat'];
                $kelas = $sb['kelas'];
                $depart = $sb['departemen'];
            }
            $sumk = array_sum($arrdetail); 
            // $total_kk[] = $sumk;

            $arrdata[] = array('kk_no' => $kk, 'total' => $arrdetail, 'tingkat' => $tingkat, 'kelas' => $kelas, 'departemen' => $depart, 'grandtotal' => $sumk);
        }

        foreach($departemen as $dpt) {
            $array_detail_table = [];
            foreach($arrgroup as $key => $ssb) {
                $src_data = $this->searchArrId('departemen', $dpt['departemen'], $ssb);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;    
                }
                $array_detail_table[] = $total2;
            }  
            $arrdep[] = $dpt['departemen'];
            
            $total = array_sum($array_detail_table);
            $arrsum[] = array($dpt['departemen'] => $total);        
        }
            
        return response()->json([
            'dataseries' => $arrdata,
            'header' => $arrdep,
            'sum' => $arrsum
        ]);
    }

    public function change(Request $request) {
        $akad = DB::connection('mysqlakad');
        
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
            $array_table[] = array('departemen' => $prodep['departemen'], 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
            // print_r($total_by_departemen);
        }
      
        // print_r($total_by_departemen);die;   
        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata, 
            'sum_ta' => $total_by_tahunajaran,
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

    public function FilterTkt(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_tingkat = ($request->tingkat)? "t.tingkat='".$request->tingkat."'" : "";
        $filter_tahunajaran = ($request->tahunajaran) ? "ta.tahunajaran ='".$request->tahunajaran."'" : "";
        $condition = ($filter_tingkat && $filter_tahunajaran) ? " and " : "";

        // where clause tingkat
        $clause_tingkat_kelas = ($filter_tingkat || $filter_tahunajaran) ? "where ".$filter_tingkat.$condition.$filter_tahunajaran : "";

        // main query
        $main_query = "select count(k.replid) as total, ta.tahunajaran as tahunajaran, t.tingkat as tingkat from kelas as k inner join tingkat as t on k.idtingkat = t.replid inner join tahunajaran as ta on k.    idtahunajaran = ta.replid $clause_tingkat_kelas group by ta.replid, t.tingkat";

        $jml_tkt_kelas = $akad->select($main_query);

        $jml_tkt_kelas = $this->changeToArr($jml_tkt_kelas);

        $array_group = [];
        foreach ($jml_tkt_kelas as $key => $item) {
            $array_group[$item['tahunajaran']][$key] =$item;
        }

        
        //  where clause departemen 
        $clause_tingkat = ($filter_tingkat || $filter_tahunajaran) ? "where ".$filter_tingkat : "";

        // find tingkat
        $tingkat = $akad->select("select distinct tingkat from tingkat as t inner join departemen as d on t.departemen = d.departemen inner join tahunajaran as ta on d.departemen = ta.departemen $clause_tingkat order by t.urutan asc");

        $tingkat = $this->changeToArr($tingkat);

        $array_table = [];
        $arraydata = [];
        $total_by_tahunajaran = [];
        $total_by_tingkat = [];
        $arr_headerta = [];

        foreach($array_group as $key => $arrth) {
            $array_detail = [];
            $arr_total = [];
            foreach($tingkat as $tkt) {
                $src_data = $this->searchArrId('tingkat', $tkt['tingkat'], $arrth);
                $total = $src_data['total'];
                if ($src_data == null) {
                    $total = 0;
                }
                $array_detail[] = $total;
                $arr_total[]  = $total;
            }

            $sum = array_sum($arr_total);
            $total_by_tahunajaran[] = $sum;

            $arr_headerta[] = $key;
            
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail);
            $arraydata[] = $array_series;

        }

        // array for table
        foreach ($tingkat as $tnkt) {
            $array_detail_table = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchArrId('tingkat', $tnkt['tingkat'], $value);
                $total2 = $src_data['total'];
                if ($src_data == null) {
                    $total2 = 0;
                }
                $array_detail_table[] = $total2;
            }

            $total_by_tingkat = array_sum(array_values($array_detail_table));
            $array_table[] = array('tingkat' => $tnkt['tingkat'], 'total' => $array_detail_table,'grandtotal' => $total_by_tingkat);
            // print_r($total_by_departemen);
        }

        return response()->json([
            'data_table' => $array_table,
            'dataset' => $arraydata,
            'sum_thj' => $total_by_tahunajaran,
            'header' => $arr_headerta
        ],200);
    } 

    // public function filterSibling(Request $request) {
    //     $akad = DB::connection('mysqlakad');

    //     $tingkat = ($request->tingkat) ? "t.tingkat = '".$request->tingkat."' AND" : "";
    //     $aktif = ($request->aktifta)  ? "AND ta.aktif = ".$request->tahunajaran." : "";

        // $main_query = "SELECT COUNT(s.replid) as total, s.kk_no as kk_no, k.kelas as kelas,t.tingkat as tingkat, ta.departemen as departemen FROM siswa_saudara AS ss INNER JOIN siswa AS s ON ss.siswa_id = s.replid INNER JOIN kelas AS k ON k.replid = s.idkelas INNER JOIN tahunajaran AS ta ON ta.replid = k.idtahunajaran INNER JOIN tingkat as t ON k.idtingkat = t.replid WHERE $tingkat (s.kk_no!='' OR s.kk_no!=null) AND s.alumni = 0 $aktif GROUP BY s.kk_no ORDER BY s.kk_no ASC";

        // $siblings = $akad->select($main_query); 
        // $siblings =  $this->changeToArr($siblings);
 
        // $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen inner join kelas as k on k.idtahunajaran = ta.replid order by urutan asc");
        
        // $departemen = $this->changeToArr($departemen);

        // $arrgroup = [];
        // foreach($siblings as $key => $sib) {
        //     $arrgroup[$sib['kk_no']][$key] = $sib;
        // } 

        // $arrsum = [];
        // $arrdep = [];
        // $arrdata = [];
        // foreach($arrgroup as $key => $ssb) {
        //     $arrdetail = [];
        //     foreach($departemen as $dpt) {
        //         $src_data = $this->searchArrId('departemen', $dpt['departemen'], $ssb);
        //         if($src_data == null) {
        //             $src_data['total'] = 0;
        //         }
        //         $arrdetail[] = $src_data['total'];
        //     }   
           
        //     foreach($ssb as $sb) {
        //         $kk = $sb['kk_no'];
        //         $tingkat = $sb['tingkat'];
        //         $kelas = $sb['kelas'];
        //         $depart = $sb['departemen'];
        //     }
        //     $sumk = array_sum($arrdetail); 
        //     // $total_kk[] = $sumk;

        //     $arrdata[] = array('kk_no' => $kk, 'total' => $arrdetail, 'tingkat' => $tingkat, 'kelas' => $kelas, 'departemen' => $depart, 'grandtotal' => $sumk);
        // }

        // foreach($departemen as $dpt) {
        //     $array_detail_table = [];
        //     foreach($arrgroup as $key => $ssb) {
        //         $src_data = $this->searchArrId('departemen', $dpt['departemen'], $ssb);
        //         $total2 = $src_data['total'];
        //         if ($src_data == null) {
        //             $total2 = 0;    
        //         }
        //         $array_detail_table[] = $total2;
        //     }  
        //     $arrdep[] = $dpt['departemen'];
            
        //     $total = array_sum($array_detail_table);
        //     $arrsum[] = array($dpt['departemen'] => $total);        
        // }
            
        // return response()->json([
        //     'dataseries' => $arrdata,
        //     'sum' => $arrsum
        // ]);
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
}
