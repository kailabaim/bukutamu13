<?php

namespace App\Http\Controllers;

use App\Models\OrangTua;
use App\Models\Instansi;
use App\Models\TamuUmum;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class KunjunganController extends Controller
{
    public function index()
    {
        // Summary statistics
        $totalOrangTua = OrangTua::count();
        $totalInstansi = Instansi::count();
        $totalTamuUmum = TamuUmum::count();
        
        $stats = [
            'total_tamu' => $totalOrangTua + $totalInstansi + $totalTamuUmum,
            'total_kunjungan' => $totalOrangTua + $totalInstansi + $totalTamuUmum,
            'tamu_orangtua' => $totalOrangTua,
            'tamu_instansi' => $totalInstansi,
            'tamu_tamuUmum' => $totalTamuUmum
        ];

        // Performance data for chart
        $performanceData = [];
        
        $currentYear = now()->year;
        $year = $currentYear;
        
        // cek ada data di tahun sekarang
        $hasCurrentYearData = OrangTua::whereYear('tanggal', $currentYear)->exists() || 
                              Instansi::whereYear('tanggal_kunjungan', $currentYear)->exists() ||
                              TamuUmum::whereYear('tanggal_kunjungan', $currentYear)->exists();
        
        if (!$hasCurrentYearData) {
            $lastOrangTuaYear = OrangTua::selectRaw('YEAR(tanggal) as year')
                ->orderBy('tanggal', 'desc')
                ->first();

            $lastTamuUmumYear = TamuUmum::selectRaw('YEAR(tanggal_kunjungan) as year')
                ->orderBy('tanggal_kunjungan', 'desc')
                ->first();
            
            $lastInstansiYear = Instansi::selectRaw('YEAR(tanggal_kunjungan) as year')
                ->orderBy('tanggal_kunjungan', 'desc')
                ->first();
            
            if ($lastOrangTuaYear || $lastInstansiYear || $lastTamuUmumYear) {
                $year1 = $lastOrangTuaYear ? $lastOrangTuaYear->year : 0;
                $year2 = $lastInstansiYear ? $lastInstansiYear->year : 0;
                $year3 = $lastTamuUmumYear ? $lastTamuUmumYear->year : 0;
                $year = max($year1, $year2, $year3);
            }
        }
        
        // data bulanan
        for ($month = 1; $month <= 12; $month++) {
            $orangTuaCount = OrangTua::whereYear('tanggal', $year)
                                   ->whereMonth('tanggal', $month)
                                   ->count();
            
            $instansiCount = Instansi::whereYear('tanggal_kunjungan', $year)
                                   ->whereMonth('tanggal_kunjungan', $month)
                                   ->count();

            $tamuUmumCount = TamuUmum::whereYear('tanggal_kunjungan', $year)
                                   ->whereMonth('tanggal_kunjungan', $month)
                                   ->count();
            
            $performanceData[] = $orangTuaCount + $instansiCount + $tamuUmumCount;
        }

        // Latest visits
        $latestOrangTua = OrangTua::select([
            'nama_orangtua as nama',
            'tanggal',
            'keperluan',
            \DB::raw("'Orang Tua' as tipe")
        ]);

        $latestInstansi = Instansi::select([
            'nama',
            'tanggal_kunjungan as tanggal',
            'keperluan',
            \DB::raw("'Instansi' as tipe")
        ]);

        $latestTamuUmum = TamuUmum::select([
            'nama',
            'tanggal_kunjungan as tanggal',
            'keperluan',
            \DB::raw("'Tamu Umum' as tipe")
        ]);

        $latestVisits = $latestOrangTua
            ->union($latestInstansi)
            ->union($latestTamuUmum)
            ->orderBy('tanggal', 'desc')
            ->limit(5)
            ->get();

        return view('kunjungan', compact('stats', 'performanceData', 'latestVisits', 'year'));
    }
}
