<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrangTua;
use App\Models\Instansi;
use App\Models\TamuUmum;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Query langsung tanpa cache (optimized for Vercel serverless)
        $totalOrangtua = OrangTua::count();
        $totalInstansi = Instansi::count();
        $totalUmum = TamuUmum::count();

        $totalTamu = $totalOrangtua + $totalInstansi + $totalUmum;
        $totalKunjungan = $totalTamu;

        // --- Hitung jumlah per bulan (6 bulan terakhir) ---
        $dataPerBulan = [];
        $labelsPerBulan = [];

        for ($i = 5; $i >= 0; $i--) {
            $tanggal = Carbon::now()->subMonths($i);
            $labelsPerBulan[] = $tanggal->format('M');

            $countOrtu = OrangTua::whereBetween('tanggal', [
                $tanggal->copy()->startOfMonth(),
                $tanggal->copy()->endOfMonth()
            ])->count();

            $countInstansi = Instansi::whereBetween('tanggal_kunjungan', [
                $tanggal->copy()->startOfMonth(),
                $tanggal->copy()->endOfMonth()
            ])->count();

            $countUmum = TamuUmum::whereBetween('tanggal_kunjungan', [
                $tanggal->copy()->startOfMonth(),
                $tanggal->copy()->endOfMonth()
            ])->count();

            $dataPerBulan[] = $countOrtu + $countInstansi + $countUmum;
        }

        // --- Pie chart distribusi tamu ---
        $chartData = [
            'labels' => ['Orang Tua', 'Instansi', 'Umum'],
            'data' => [$totalOrangtua, $totalInstansi, $totalUmum],
            'colors' => ['#10b981', '#3b82f6', '#f59e0b'],
        ];

        // --- Tamu terbaru ---
        $latestOrangTua = OrangTua::select([
            'nama_orangtua as nama',
            'tanggal',
            'keperluan',
            DB::raw("'Orang Tua' as tipe")
        ])->latest('tanggal')->limit(3);

        $latestInstansi = Instansi::select([
            'nama',
            'tanggal_kunjungan as tanggal',
            'keperluan',
            DB::raw("'Instansi' as tipe")
        ])->latest('tanggal_kunjungan')->limit(3);

        $latestUmum = TamuUmum::select([
            'nama',
            'tanggal_kunjungan as tanggal',
            'keperluan',
            DB::raw("'Umum' as tipe")
        ])->latest('tanggal_kunjungan')->limit(3);

        // Union 3 query (masing-masing sudah limit)
        $combinedSql = $latestOrangTua
            ->unionAll($latestInstansi)
            ->unionAll($latestUmum);

        $latestGuests = DB::table(DB::raw("({$combinedSql->toSql()}) as combined"))
            ->mergeBindings($latestOrangTua->getQuery())
            ->orderByDesc('tanggal')
            ->limit(8)
            ->get();

        return view('dashboard', compact(
            'totalTamu',
            'totalKunjungan',
            'totalOrangtua',
            'totalInstansi',
            'totalUmum',
            'latestGuests',
            'dataPerBulan',
            'labelsPerBulan',
            'chartData'
        ));
    }

    /**
     * API untuk meta terbaru dashboard.
     */
    public function latestMeta(Request $request)
    {
        // Gunakan query minimum (ambil ID terakhir aja)
        $latest = collect([
            ['type' => 'orangtua', 'id' => OrangTua::max('id')],
            ['type' => 'instansi', 'id' => Instansi::max('id')],
            ['type' => 'umum', 'id' => TamuUmum::max('id')],
        ])->filter(fn($x) => $x['id'])->sortByDesc('id')->first();

        $latestType = $latest['type'] ?? null;
        $latestId = (int) ($latest['id'] ?? 0);

        return response()->json([
            'latest_type' => $latestType,
            'latest_id' => $latestId,
            'counts' => [
                'orangtua' => OrangTua::count(),
                'instansi' => Instansi::count(),
                'umum' => TamuUmum::count(),
                'total' => OrangTua::count() + Instansi::count() + TamuUmum::count(),
            ],
        ]);
    }

    /**
     * Event Stream (SSE) - Real-time update
     * Note: SSE might not work reliably on Vercel serverless
     */
    public function stream(Request $request)
    {
        ignore_user_abort(true);
        set_time_limit(0);

        $headers = [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
            'Connection' => 'keep-alive',
        ];

        return response()->stream(function () {
            $lastSent = 0;

            $getLatest = function () {
                $latest = collect([
                    ['type' => 'Orang Tua', 'created_at' => optional(OrangTua::latest('created_at')->first())->created_at],
                    ['type' => 'Instansi', 'created_at' => optional(Instansi::latest('created_at')->first())->created_at],
                    ['type' => 'Umum', 'created_at' => optional(TamuUmum::latest('created_at')->first())->created_at],
                ])->filter(fn($x) => $x['created_at'])->sortByDesc('created_at')->first();

                return [
                    'latest_type' => $latest['type'] ?? null,
                    'latest_created_at' => optional($latest['created_at'])->timestamp ?? 0,
                ];
            };

            $sendEvent = function ($payload) use (&$lastSent) {
                $id = $payload['latest_created_at'] ?? time();
                $lastSent = $id;
                echo "id: {$id}\n";
                echo "event: guest\n";
                echo 'data: ' . json_encode($payload) . "\n\n";
                @ob_flush();
                @flush();
            };

            // Initial event
            $meta = $getLatest();
            $sendEvent($meta);

            while (!connection_aborted()) {
                $current = $getLatest();
                if (($current['latest_created_at'] ?? 0) > $lastSent) {
                    $sendEvent($current);
                }
                sleep(3);
            }
        }, 200, $headers);
    }
}