<?php

namespace App\Exports;

use App\Models\OrangTua;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class OrangTuaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell
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
        $query = OrangTua::query();

        if ($this->search) {
            $query->where(function($q) {
                $q->where('nama_orangtua', 'like', "%{$this->search}%")
                  ->orWhere('nama_siswa', 'like', "%{$this->search}%")
                  ->orWhere('alamat', 'like', "%{$this->search}%")
                  ->orWhere('keperluan', 'like', "%{$this->search}%")
                  ->orWhere('guru_dituju', 'like', "%{$this->search}%")
                  ->orWhere('kelas', 'like', "%{$this->search}%");
            });
        }

        if ($this->bulan) {
            $query->whereMonth('tanggal', $this->bulan); // Filter berdasarkan kolom 'tanggal'
        }

        if ($this->sort === 'oldest') {
            $query->orderBy('tanggal', 'asc'); // Sort berdasarkan kolom 'tanggal'
        } else {
            $query->orderBy('tanggal', 'desc'); // Sort berdasarkan kolom 'tanggal'
        }

        return $query->get();
    }

    // ✅ Headings DENGAN kolom "No"
    public function headings(): array
    {
        return [
            'No',
            'Nama Orang Tua',
            'Nama Siswa',
            'Alamat',
            'Keperluan',
            'Kontak',
            'Guru Dituju',
            'Kelas',
            'Waktu Kunjungan',
            'Tanggal Kunjungan',
        ];
    }

    // ✅ Map DENGAN nomor urut iterasi
    public function map($ortu): array
    {
        return [
            $this->rowNumber++,
            $ortu->nama_orangtua,
            $ortu->nama_siswa,
            $ortu->alamat,
            $ortu->keperluan,
            $ortu->kontak ?? '-',
            $ortu->guru_dituju,
            $ortu->kelas,
            $ortu->waktu_kunjungan 
                ? \Carbon\Carbon::parse($ortu->waktu_kunjungan)->format('H:i')
                : '-',
            $ortu->tanggal 
                ? \Carbon\Carbon::parse($ortu->tanggal)->translatedFormat('d F Y') 
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
        $sheet->setCellValue('A1', 'LAPORAN DATA TAMU ORANG TUA ' . strtoupper($namaBulan));
        $sheet->mergeCells('A1:J1');
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Style header tabel (baris 2)
        $sheet->getStyle('A2:J2')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => 'solid', 'color' => ['rgb' => '4CAF50']],
            'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
        ]);

        // Border semua cell
        $sheet->getStyle('A2:J' . $lastRow)
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