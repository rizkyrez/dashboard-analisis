<?php

namespace App\Http\Controllers;

use DeepCopy\f001\A;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class KepegawaianController extends Controller
{
    public function AgamaPegawai() {
        $sdm = DB::connection('mysqlsdm');

        // main query
        $agama = $sdm->select("select count(p.replid) as total, (CASE WHEN p.agama is null OR p.agama = '' THEN 'NULL' ELSE ag.agama END) as agama from jbssdm.pegawai as p left join jbsumum.agama as ag on p.agama = ag.agama group by ag.agama order by urutan ASC");

        $agama = $this->changeToArr($agama);

        $arraydata = [];
        foreach ($agama as $agd) {
            $arraydata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function PendidikanPegawai() {
        $sdm = DB::connection('mysqlsdm');

        // main query
        $pendidikan = $sdm->select("select count(p.replid) as total, (CASE WHEN ps.tingkat is null OR ps.tingkat = '' THEN 'NULL' ELSE ps.tingkat END) as tingkat from jbssdm.pegawai as p inner join jbssdm.pegsekolah as ps on p.nip = ps.nip inner join jbsumum.tingkatpendidikan as pd on ps.tingkat = pd.pendidikan group by ps.tingkat");

        $pendidikan = $this->changeToArr($pendidikan);

        $arraydata = [];
        foreach ($pendidikan as $pdk) {
        $arraydata[] = array('value' => $pdk['total'], 'name' => $pdk['tingkat']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function MasaKerjaPegawai() {
        $sdm = DB::connection('mysqlsdm');  

        // main query
        $masa_kerja = $sdm->select("select (CASE WHEN  p.mulaikerja = '0000-00-00' THEN 'NULL' ELSE TIMESTAMPDIFF(YEAR,p.mulaikerja,CURRENT_DATE()) END) as lama_kerja, count(p.replid) as total from jbssdm.pegawai as p where p.aktif = 1 group by lama_kerja order by lama_kerja desc");

        $masa_kerja = $this->changeToArr($masa_kerja);

        asort($masa_kerja);

        $arraydata = [];
        foreach ($masa_kerja as $msk) {
            $arraydata[] = array('masa_kerja' => $msk['lama_kerja'], 'value' => $msk['total']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function KeluargaPegawai() {
        $sdm = DB::connection('mysqlsdm');  

        // main query
        $masa_kerja = $sdm->select("select count(pkg.replid) as total, p.nip as nip, pkg.hubungan as hubungan, (CASE WHEN p.bagian is null OR p.bagian = '' THEN 'NULL' ELSE bp.bagian END) as bagian from pegkeluarga as pkg inner join pegawai as p on pkg.nip = p.nip inner join bagianpegawai as bp on p.bagian = bp.bagian where p.aktif = 1 and pkg.hubungan = 'Orang Tua' group by p.bagian");

        $masa_kerja = $this->changeToArr($masa_kerja);

        $arraydata = [];
        foreach ($masa_kerja as $msk) {
            $jenpeg = "";
            if(($msk['bagian'] == "akademik" ) || ($msk['bagian'] == "Akademik"))  {
                $jenpeg = "Guru";
            }
            else if($msk['bagian'] == "non akademik") {
                $jenpeg = "Karyawan";
            }
            else if($msk['bagian'] == "NULL") {
                $jenpeg = "NULL";
            }
            $arraydata[] = array('value' => $msk['total'], 'name' => $jenpeg);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]); 
    }

    public function SumPunishment() {
        $sdm = DB::connection('mysqlsdm');
        $akad = DB::connection('mysqlakad'); 

        $sum_punish = $sdm->select("SELECT COUNT(trp.id) as total, stk.satker as satker, p.departement as dep FROM tr_punishment AS trp INNER JOIN master_violation AS mv ON mv.id=trp.violation_id INNER JOIN master_punishment AS mp ON mp.punishment_id=trp.punishment_id INNER JOIN pegawai AS p ON p.nip=trp.nip inner join jbsakad.departemen as dp on p.departement = dp.departemen inner join satker as stk on p.satker_id = stk.replid where CURRENT_DATE <= trp.expired_date group by dp.departemen");

        $sum_punish =  $this->changeToArr($sum_punish);

        $departemen = $akad->select("select distinct departemen as departemen from departemen order by urutan asc");

        $departemen =  $this->changeToArr($departemen);

        $arrdata = [];
        foreach($departemen as $dpm) {
            $src_data = $this->searchArrId('dep', $dpm['departemen'], $sum_punish);
            $total = $src_data['total'];
            if ($src_data == null) {
                $total = 0;
            }
            $arrdata[] = array('value' => $total, 'dep' => $dpm['departemen']);
        }
    
        return response()->json([
            'dataset' => $arrdata
        ]);
    }

    public function ListFilterPegawai() {
        $akad = DB::connection('mysqlakad');
        $umum = DB::connection('mysqlumum');  
        $sdm = DB::connection('mysqlsdm'); 

        // query list option departemen
        $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join tahunajaran as ta on dp.departemen = ta.departemen order by urutan asc");

        // query list option proses
        $list_tahunajaran = $akad->select("select distinct ta.tahunajaran as tahunajaran from tahunajaran as ta inner join kelas as k on k.idtahunajaran = ta.replid group by ta.replid,ta.departemen");

        $list_bagianpegawai  = $sdm->select('select bagian from bagianpegawai order by urutan asc');    

        $array_departemen = ['All'];
        $array_tahunajaran = ['All'];
        $array_bagian = ['Semua Bagian'];

        foreach($list_tahunajaran as $thj) {
            $array_tahunajaran[] = $thj->tahunajaran;
        }

        foreach($list_bagianpegawai as $bp) {
            $array_bagian[] = $bp->bagian;
        }
        
        foreach($departemen as $depart) {
            $array_departemen[] = $depart->departemen;
        }

        return response()->json([
            'list_bg' => $array_bagian,
            'list_dp' => $array_departemen,
            'list_tahunajaran' => $array_tahunajaran,
        ]);
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
        $akad =  DB::connection('mysqlsdm');

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
