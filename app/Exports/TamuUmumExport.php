<?php

namespace App\Exports;

use App\Models\TamuUmum;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class TamuUmumExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell
{
    protected $bulan;
    protected $search;
    protected $sort;
    protected $rowNumber = 1;

    public function __construct($bulan = null, $search = null, $sort = 'newest')
    {
        $this->bulan = $bulan;
        $this->search = $search;
        $this->sort = $sort;
    }

    public function collection()
    {
        $query = TamuUmum::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama', 'like', "%{$this->search}%")
                  ->orWhere('identitas', 'like', "%{$this->search}%")
                  ->orWhere('keperluan', 'like', "%{$this->search}%")
                  ->orWhere('guru_dituju', 'like', "%{$this->search}%")
                  ->orWhere('kontak', 'like', "%{$this->search}%");
        });
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal_kunjungan', $this->bulan);
        }

        if ($this->sort === 'oldest') {
            $query->orderBy('tanggal_kunjungan', 'asc');
        } else {
            $query->orderBy('tanggal_kunjungan', 'desc');
        }

        return $query->get();
    }

    // ✅ Headings DENGAN kolom "No"
    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'Identitas',
            'Keperluan',
            'Guru Yang Dituju',
            'Kontak',
            'Waktu Kunjungan',
            'Tanggal Kunjungan',
        ];
    }

    // ✅ Map DENGAN nomor urut iterasi dan urutan kolom benar
    public function map($tamu_umum): array
    {
        return [
            $this->rowNumber++,
            $tamu_umum->nama,
            $tamu_umum->identitas,
            $tamu_umum->keperluan,
            $tamu_umum->guru_dituju,
            $tamu_umum->kontak ?? '-',
            $tamu_umum->waktu_kunjungan 
                ? \Carbon\Carbon::parse($tamu_umum->waktu_kunjungan)->format('H:i:s')
                : '-',
            $tamu_umum->tanggal_kunjungan 
                ? \Carbon\Carbon::parse($tamu_umum->tanggal_kunjungan)->translatedFormat('d F Y') 
                : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $bulan = request('bulan');
        $namaBulan = $bulan 
            ? Carbon::createFromFormat('!m', $bulan)->translatedFormat('F') 
            : 'Semua Bulan';

        // Judul di baris 1
        $sheet->setCellValue('A1', 'LAPORAN DATA TAMU UMUM ' . strtoupper($namaBulan));
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Style header tabel (baris 2)
        $sheet->getStyle('A2:H2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4CAF50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Border semua cell
        $sheet->getStyle('A2:H' . $lastRow)
              ->applyFromArray([
                  'borders' => [
                      'allBorders' => [
                          'borderStyle' => 'thin',
                          'color' => ['rgb' => '000000'],
                      ],
                  ],
              ]);

        return [];
    }

    public function startCell(): string
    {
        return 'A2'; // Header mulai dari baris 2, data mulai dari baris 3
    }
}
