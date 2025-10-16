<?php

namespace App\Exports;

use App\Models\Promo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;

class PromoExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    private $rowNumber = 0;
    public function collection()
    {
        return Promo::all();
    }


    public function headings():array
    {
        return ['No', 'Kode', 'Tipe', 'Jumlah'];
    }

    public function map($promo): array
    {
        return [
            //increment rownumber yang sebelumnanya 0, tiap mapping (looping) data
            ++$this->rowNumber,
            $promo->promo_code,
            ucfirst($promo->type),
            $promo->type == 'percent' ? $promo->discount . '%' : 'Rp ' . number_format($promo->discount, 0, ',', '.'),
        ];
    }
}
