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

    public function change(Request $request) {
        $akad = DB::connection('mysqlakad');
        
        // fetch request
        $filter_tahunajaran = ($request->departmen) ? "where ta.departemen ='".$request->departmen."'" : "";

        // query list option tahunajaran
        $tahunajaran = $akad->select("select distinct ta.tahunajaran as tahunajaran from tahunajaran as ta inner join departemen as dp on ta.departemen = dp.departemen inner join jbssdm.pegawai as p on p.departement = dp.departemen $filter_tahunajaran group by ta.replid,ta.departemen");

        $array_tahunajaran = ['All'];

        foreach($tahunajaran as $thj) {
            $array_tahunajaran[] = $thj->tahunajaran;
        }

        return response()->json([
            'list_tahunajaran' => $array_tahunajaran,
        ],200);
    }

    public function ChangeBagian(Request $request) {
        $sdm = DB::connection('mysqlsdm');
        
        // fetch request
        $filter_bagian = ($request->bagian) ? "where p.bagian ='".$request->bagian."'" : "";

        // query list option tahunajaran
        $departemen = $sdm->select("select distinct dp.departemen as departemen from pegawai as p inner join bagianpegawai as bg on p.bagian = bg.bagian inner join jbsakad.departemen as dp on p.departement = dp.departemen $filter_bagian group by dp.departemen");

        $array_departemen = ['All'];

        foreach($departemen as $dpm) {
            $array_departemen[] = $dpm->departemen;
        }

        return response()->json([
            'list_dp' => $array_departemen,
        ],200);
    }

    public function FilterAg(Request $request) {
        $sdm = DB::connection('mysqlsdm');

        // fetch request
        $filter_departemen = ($request->departmen) ? "ta.departemen ='".$request->departmen."'" : "";
        $filter_tahunajaran = ($request->tahunajaran) ? "ta.tahunajaran ='".$request->tahunajaran."'" : "";
        $filter_agama = ($request->agama)? "ag.agama='".$request->agama."'" : "";
        $condition = ($filter_departemen && $filter_tahunajaran) ? " and " : "";

        if($request->agama != "") { 
            $condition2 = ($filter_departemen == "" && $filter_tahunajaran == "") ? "" : " and ";
        } 

        $query_filter = (isset($condition2)) ? $filter_departemen.$condition.$filter_tahunajaran.$condition2.$filter_agama : $filter_departemen.$condition.$filter_tahunajaran;
        $clause_agama_pegawai = ($filter_departemen || $filter_tahunajaran) ? "where ".$query_filter : "";

        // main query
        $agama = $sdm->select("select count(p.replid) as total, ag.agama as agama from jbssdm.pegawai as p left join jbsumum.agama as ag on p.agama = ag.agama inner join jbsakad.departemen as dp on p.departement = dp.departemen inner join jbsakad.tahunajaran as ta on dp.departemen = ta.departemen $clause_agama_pegawai group by ag.agama order by ag.urutan ASC");

        $agama = $this->changeToArr($agama);

        $arraydata = [];
        foreach ($agama as $agd) {
            $arraydata[] = array('value' => $agd['total'], 'name' => $agd['agama']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function FilterPendidikan(Request $request) {
        $sdm = DB::connection('mysqlsdm');

        // fetch request
        $filter_departemen = ($request->departemen) ? "where p.departement ='".$request->departemen."'" : "";
        
        // main query
        $pendidikan = $sdm->select("select count(p.replid) as total, ps.tingkat as tingkat from pegawai as p inner join pegsekolah as ps on p.nip = ps.nip inner join jbsumum.tingkatpendidikan as pd on ps.tingkat = pd.pendidikan $filter_departemen group by ps.tingkat");

        $pendidikan = $this->changeToArr($pendidikan);

        $arraydata = [];
        foreach ($pendidikan as $pdk) {
        $arraydata[] = array('value' => $pdk['total'], 'name' => $pdk['tingkat']);
        }
        
        return response()->json([
            'dataset' => $arraydata
        ]);
    }

    public function FilterMasaKerja(Request $request) {
        $sdm = DB::connection('mysqlsdm');

        // fetch request
        $filter_departemen = ($request->departemen) ? "and p.departement ='".$request->departemen."'" : "";
        
        // main query
        $masa_kerja = $sdm->select("select (CASE WHEN  p.mulaikerja = '0000-00-00' THEN 'NULL' ELSE TIMESTAMPDIFF(YEAR,p.mulaikerja,CURRENT_DATE()) END) as lama_kerja, count(p.replid) as total from jbssdm.pegawai as p where p.aktif = 1 $filter_departemen group by lama_kerja order by lama_kerja desc");

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
