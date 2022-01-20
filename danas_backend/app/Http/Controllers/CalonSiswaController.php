<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class CalonSiswaController extends Controller
{
    public function trendcs(Request $request) {
        $akad =  DB::connection('mysqlakad');
        $filter_departemen = $request->departemen;
        $filter_proses = $request->proses_id;
        
        // main query
        // $trendcs = $akad->select("select count(cs.replid) as total, pps.departemen, pps.proses from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid group by pps.replid, pps.departemen");

        $trendcs2 = $akad->table('calonsiswa')
        ->select(array('prosespenerimaansiswa.replid', 'prosespenerimaansiswa.departemen', 'prosespenerimaansiswa.proses', DB::raw('COUNT(calonsiswa.replid) as total')))
        ->join('prosespenerimaansiswa','prosespenerimaansiswa.replid','=', 'calonsiswa.idproses')
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('prosespenerimaansiswa.departemen', $filter_departemen);
        })
        ->when($filter_proses, function ($query, $filter_proses) {
            return $query->where('prosespenerimaansiswa.replid', $filter_proses);
        })
        ->groupBy('prosespenerimaansiswa.departemen', 'prosespenerimaansiswa.replid', 'prosespenerimaansiswa.proses')
        ->orderBy('prosespenerimaansiswa.replid','asc')
        ->orderBy('prosespenerimaansiswa.departemen','asc')
        ->get();

        // change to array 
//        $trendcs = $this->changeToArr($trendcs);

        // grouping by proses
        $array_group = [];
        foreach ($trendcs2 as $key => $item) {
            $array_group[$item->proses][$key] =$item;
        }
     
        // query main departemen
        // $departemen = $akad->select("select distinct dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen order by urutan asc");
        $departemen = $akad->table('departemen')
        ->select('departemen.departemen', 'departemen.replid', 'departemen.urutan')
        ->join('prosespenerimaansiswa','prosespenerimaansiswa.departemen','=', 'departemen.departemen')
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('prosespenerimaansiswa.departemen', $filter_departemen);
        })
        ->groupBy('departemen.departemen', 'departemen.replid', 'departemen.urutan')
        ->orderBy('departemen.urutan','asc')
        ->get();
     
        // change to array  
        // $departemen = $this->changeToArr($departemen);


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
               $src_data = $this->searchObjectId('departemen', $dep->departemen, $arr_proses);
               $total = $src_data?$src_data->total:0;
               $array_detail[] = array($dep->departemen,$total);
               $arr_total[]  = $total;
            }
            $sum = array_sum($arr_total);
            $total_by_proses[] = $sum;
            $arr_headpros[] = $key;
            $array_series = array('name' => $key, 'type' => 'bar','data' => $array_detail, 'animationDurationUpdate' => 500, 'universalTransition' => array('enabled' =>  true, 'divideShape' =>'clone'));
            $arraydata[] = $array_series;   
        }

        // array for table
        foreach ($departemen as $prodep) {
            $array_detail_table = [];
            $array_prs = [];
            foreach ($array_group as $key => $value) {
                $src_data = $this->searchObjectId('departemen',$prodep->departemen, $value);
                $total2 = $src_data?$src_data->total:0;
                $array_detail_table[] = $total2;
                $array_prs[] = $key;
            }

            $total_by_departemen = array_sum(array_values($array_detail_table));
            $array_table[] = array('departemen' => $prodep->departemen, 'proses'=> $array_prs, 'total' => $array_detail_table,'grandtotal' => $total_by_departemen);
        }

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
        $proses = $akad->select("select pps.replid, pps.departemen, pps.aktif, pps.proses COLLATE utf8_bin as proses from prosespenerimaansiswa as pps  $filter_proses ");

        $array_proses = [];
        if($request->departmen){
            foreach($proses as $prs) {
                $array_proses[] =  array('id'=> $prs->replid, 'label'=>($prs->aktif > 0)?$prs->proses.' (Aktif)':$prs->proses);
            }
        }
        
        return response()->json([
            'list_proses' => $array_proses,
        ],200);
    }

    public function filter(Request $request) {
        $akad =  DB::connection('mysqlakad');

        // fetch request
        $filter_departemen = ($request->departmen) ? "pps.departemen ='".$request->departmen."'" : "";
        $filter_proses = ($request->proses) ? "pps.replid ='".$request->proses."'" : "";
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
        $departemen = $akad->select("select  dp.departemen from departemen as dp inner join prosespenerimaansiswa as pps on dp.departemen = pps.departemen $clause_departemen order by urutan asc");

        // change to array  
        $departemen = $this->changeToArr($departemen);

        $array_table = [];
        $arraydata = [];
        $total_by_proses = [];
        $total_by_departemen = [];

        $arr_headpros = [];
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
        }

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
        $filter_agama =     ($request->agama) ? "ag.agama ='".$request->agama."'" : "";
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

    public function achievement(Request $request) {
        $akad = DB::connection('mysqlakad');
        $filter_departemen =$request->input('departemen');
        $filter_proses =$request->input('proses_id');
        $aktif = $filter_proses?false:true;

        $tahun_ajaran_active = $akad->table('tahunajaran')->where('aktif',true)
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('departemen', $filter_departemen);
        })
        ->orderBy('tglmulai', 'desc')->get();

        $proses_tahun_ajaran = $akad->table('prosespenerimaansiswa')
        ->select('tahunajaran.replid as replid', 'tahunajaran.departemen','tahunajaran.tglmulai','tahunajaran.tahunajaran')
        ->join('tahunajaran', 'prosespenerimaansiswa.idtahunajaran', '=', 'tahunajaran.replid')
        ->when($aktif, function ($query, $aktif) {
            return $query->where('tahunajaran.aktif', $aktif);
        })
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('tahunajaran.departemen', $filter_departemen);
        })
        ->when($filter_proses, function ($query, $filter_proses) {
            return $query->where('prosespenerimaansiswa.replid', $filter_proses);
        })->get();
        
        $datas = [];
        foreach ($proses_tahun_ajaran as $tahun_ajaran) {
            $tahun_ajaran_before = $akad->table('tahunajaran')
            ->where('tglmulai','<', $tahun_ajaran->tglmulai)
            ->where('departemen','=', $tahun_ajaran->departemen)
            ->orderBy('tglmulai', 'desc')
            ->first();
            
            $achievement_active = $akad->table('calonsiswa')
            ->select(DB::raw('count(calonsiswa.replid) as total, prosespenerimaansiswa.proses, prosespenerimaansiswa.replid,prosespenerimaansiswa.departemen'))
            ->join('prosespenerimaansiswa', 'calonsiswa.idproses', '=', 'prosespenerimaansiswa.replid')
            ->where('prosespenerimaansiswa.idtahunajaran','=', $tahun_ajaran->replid)
            ->where('prosespenerimaansiswa.departemen','=', $tahun_ajaran->departemen)
            ->groupBy('prosespenerimaansiswa.departemen', 'prosespenerimaansiswa.replid', 'prosespenerimaansiswa.proses')
            ->first();
            
            $total_before = 0;
            $prosespenerimaansiswa_before_id = '';
            $prosespenerimaansiswa_before_name = '';
            if (!empty($tahun_ajaran_before)){
                $achievement_before = $akad->table('calonsiswa')
                ->select(DB::raw('count(calonsiswa.replid) as total, prosespenerimaansiswa.proses, prosespenerimaansiswa.replid,prosespenerimaansiswa.departemen'))
                ->join('prosespenerimaansiswa', 'calonsiswa.idproses', '=', 'prosespenerimaansiswa.replid')
                ->where('prosespenerimaansiswa.idtahunajaran','=', $tahun_ajaran_before->replid)
                ->where('prosespenerimaansiswa.departemen','=', $tahun_ajaran_before->departemen)
                ->groupBy('prosespenerimaansiswa.departemen', 'prosespenerimaansiswa.replid', 'prosespenerimaansiswa.proses')
                ->first();
                $total_before = $achievement_before?$achievement_before->total:0;
                $prosespenerimaansiswa_before_id = $achievement_before?$achievement_before->replid:'';
                $prosespenerimaansiswa_before_name = $achievement_before?$achievement_before->proses:'';;
            }

            if ($achievement_active) {
                $total_aktif = $achievement_active?$achievement_active->total:0;
                $prosentase_achievement= ($total_before > 0 && $total_aktif > 0)?(($total_aktif/$total_before)*100):0;
                $datas[] = array(
                    'departemen' => $tahun_ajaran->departemen,
                    'prosespenerimaansiswa_aktif_id' => $achievement_active?$achievement_active->replid:'',
                    'prosespenerimaansiswa_aktif_name' => $achievement_active?$achievement_active->proses:'',
                    'total_aktif' => $achievement_active?$achievement_active->total:0,
                    'prosespenerimaansiswa_before_id' => $prosespenerimaansiswa_before_id,
                    'prosespenerimaansiswa_before_name' => $prosespenerimaansiswa_before_name,
                    'total_before' => $total_before,
                    'prosentase_achievement' => round($prosentase_achievement, 2)
                );
            }
        }
        $result = [];
        $groupbyDepartemen = $this->group_by('departemen',$datas);
        $grand_total_aktif = 0;
        $grand_total_before = 0;
        foreach ($groupbyDepartemen as $key => $item){
            $result[] = array(
                'departemen' => $key,
                'data_table'=> array(
                    $item[0]['total_before'],
                    $item[0]['total_aktif'],
                    $item[0]['prosentase_achievement'],
                ),
                'header'=> array(
                    $item[0]['prosespenerimaansiswa_before_name'],
                    $item[0]['prosespenerimaansiswa_aktif_name']
                ),
                
            );

            $grand_total_aktif = $grand_total_aktif + $item[0]['total_aktif'];
            $grand_total_before = $grand_total_before + $item[0]['total_before'];
        }

        // $query_active_proses = "select count(cs.replid) as total, pps.departemen, pps.proses, ta.tglmulai from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid where ta.aktif = 1 group by pps.replid";

        // $active_proses = $akad->select($query_active_proses);
        // $active_proses = $this->changeToArr($active_proses);

        // $query_last_proses = "select count(cs.replid) as total, pps.departemen, pps.proses, ta.tahunajaran, ta.tglmulai from calonsiswa as cs inner join prosespenerimaansiswa as pps on cs.idproses = pps.replid inner join tahunajaran as ta on pps.idtahunajaran = ta.replid where ta.tglmulai < any (select ta.tglmulai from tahunajaran as ta inner join prosespenerimaansiswa as pps on pps.idtahunajaran = ta.replid where ta.aktif = 1) group by pps.replid order by pps.replid desc limit 4";
        
        // $last_proses = $akad->select($query_last_proses);
        // $last_proses = $this->changeToArr($last_proses);

        // $count_active = [];
        // $count_last = [];
        // $proses_last = '';
        // $proses_active = '';

        // foreach ($active_proses as $key => $item) {
        //     $proses_active = $item['proses'];
        //     $count_active[] = $item['total'];
        // }

        // foreach ($last_proses as $key => $item) {
        //     $proses_last = $item['proses'];
        //     $count_last[] = $item['total'];
        // } 

        // $total_active = array_sum($count_active);
        // $total_last = array_sum($count_last);

        $arraydata = array($grand_total_before, $grand_total_aktif);
        $arrayproses = array();
        
        return response()->json([
            'datatable' => $result,
            'data_table' => $arraydata,
            'header' => $arrayproses,
        ],200);
        return response()->json($result,200);
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

    public function CsbAchievement(Request $request) {
        $akad = DB::connection('mysqlakad');
        $filter_departemen =$request->input('departemen');
        $filter_proses =$request->input('proses_id');
        $aktif = $filter_proses?false:true;

        $proses_tahun_ajaran = $akad->table('prosespenerimaansiswa')
        ->select('tahunajaran.replid as replid', 'tahunajaran.departemen','tahunajaran.tglmulai','tahunajaran.tahunajaran','prosespenerimaansiswa.proses')
        ->join('tahunajaran', 'prosespenerimaansiswa.idtahunajaran', '=', 'tahunajaran.replid')
        ->when($aktif, function ($query, $aktif) {
            return $query->where('tahunajaran.aktif', $aktif);
        })
        ->when($filter_departemen, function ($query, $filter_departemen) {
            return $query->where('tahunajaran.departemen', $filter_departemen);
        })
        ->when($filter_proses, function ($query, $filter_proses) {
            return $query->where('prosespenerimaansiswa.replid', $filter_proses);
        })->get();

        $datas = [];
        foreach ($proses_tahun_ajaran as $tahun_ajaran) {
            $tahun_ajaran_before = $akad->table('tahunajaran')
            ->where('tglmulai','<', $tahun_ajaran->tglmulai)
            ->where('departemen','=', $tahun_ajaran->departemen)
            ->orderBy('tglmulai', 'desc')
            ->first();
            
            $csb_internal = $akad->table('calonsiswa')
            ->select(DB::raw('count(calonsiswa.replid) as total, prosespenerimaansiswa.proses, prosespenerimaansiswa.idtahunajaran,prosespenerimaansiswa.departemen'))
            ->join('prosespenerimaansiswa', 'calonsiswa.idproses', '=', 'prosespenerimaansiswa.replid')
            ->join('tahunajaran as ta','ta.replid', '=', 'prosespenerimaansiswa.idtahunajaran')
            ->where('ta.replid', $tahun_ajaran->replid)
            ->where('ta.departemen', $tahun_ajaran->departemen)
            ->where('calonsiswa.asalsekolah', 'LIKE', '%narada%')
            ->groupBy('prosespenerimaansiswa.departemen', 'prosespenerimaansiswa.idtahunajaran', 'prosespenerimaansiswa.proses')
            ->first();

            // $last_tingkat
            $total_target_csb = 0;
            $prosespenerimaansiswa_before_id = '';
            $prosespenerimaansiswa_before_name = '';

            if ($tahun_ajaran_before) {
                $targetCsb = $akad->table('siswa as s')
                ->select(array('d.departemen','ta.replid as idtahunajaran', 'ta.tahunajaran', DB::raw('COUNT(s.replid) as total')))
                ->join('kelas as k', 'k.replid', '=', 's.idkelas')
                ->join('tahunajaran as ta','ta.replid', '=', 'k.idtahunajaran')
                ->join('tingkat as t', 't.replid', '=', 'k.idtingkat')
                ->join('departemen as d', 'd.departemen', '=', 'ta.departemen')
                ->join(DB::raw("(SELECT MAX(replid) as replid, departemen FROM tingkat GROUP by departemen) AS p"), function($join){
                    $join->on('t.replid','=','p.replid');
                    $join->on('ta.departemen','=','p.departemen');
                })
                ->where('ta.replid', $tahun_ajaran_before->replid)
                ->where('ta.departemen', $tahun_ajaran_before->departemen)
                ->groupBy('d.departemen', 'ta.replid', 'ta.tahunajaran')
                ->first();

                $total_target_csb = $targetCsb?$targetCsb->total:0;
                $prosespenerimaansiswa_before_id = $targetCsb?$targetCsb->idtahunajaran:'';
                $prosespenerimaansiswa_before_name = $targetCsb?$targetCsb->tahunajaran:'';;
            }
            
            
            $total_csb_internal = $csb_internal?$csb_internal->total:0;
            $prosentase_achievement= ($total_target_csb > 0 && $total_csb_internal > 0)?(($total_csb_internal/$total_target_csb)*100):0;
            $datas[] = array(
                'departemen' => $tahun_ajaran->departemen,
                'prosespenerimaansiswa_aktif_id' => $csb_internal?$csb_internal->idtahunajaran:'',
                'prosespenerimaansiswa_aktif_name' => $tahun_ajaran?$tahun_ajaran->proses:'',
                'total_aktif' => $total_csb_internal,
                'prosespenerimaansiswa_before_id' => $prosespenerimaansiswa_before_id,
                'prosespenerimaansiswa_before_name' => $prosespenerimaansiswa_before_name,
                'total_before' => $total_target_csb,
                'prosentase_achievement' => round($prosentase_achievement, 2).'%'
            );
        }


        $result = [];
        $groupbyDepartemen = $this->group_by('departemen',$datas);
        $grand_total_aktif = 0;
        $grand_total_before = 0;
        foreach ($groupbyDepartemen as $key => $item){
            $result[] = array(
                'departemen' => $key,
                'data_table'=> array(
                    $item[0]['total_before'],
                    $item[0]['total_aktif'],
                    $item[0]['prosentase_achievement'],
                ),
                'header'=> array(
                    $item[0]['prosespenerimaansiswa_before_name'],
                    $item[0]['prosespenerimaansiswa_aktif_name']
                ),
                
            );

            $grand_total_aktif = $grand_total_aktif + $item[0]['total_aktif'];
            $grand_total_before = $grand_total_before + $item[0]['total_before'];
        }

        $arraydata = array($grand_total_before, $grand_total_aktif);
        return response()->json([
            'datatable' => $result,
            'data_table' => $arraydata,
            
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

    protected function searchObjectId($field, $value, $array) {
        foreach ($array as $val) {
            if ($val->$field === $value) {
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

    /**
     * Function that groups an array of associative arrays by some key.
     * 
     * @param {String} $key Property to sort by.
     * @param {Array} $data Array that stores multiple associative arrays.
     */
    protected function group_by($key, $data) {
        $result = array();

        foreach($data as $val) {
            if(array_key_exists($key, $val)){
                $result[$val[$key]][] = $val;
            }else{
                $result[""][] = $val;
            }
        }

        return $result;
    }
}
