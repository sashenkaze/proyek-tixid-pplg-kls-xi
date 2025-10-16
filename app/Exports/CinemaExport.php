<?php

namespace App\Exports;

use App\Models\Cinema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class CinemaExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

     private $rowNumber = 0;
    public function collection()
    {
        return Cinema::all();
    }

    public function headings():array
    {
        return ['No', 'Nama Bioskop', 'Lokasi'];
    }

    public function map($cinema): array
    {
        return [
            //increment rownumber yang sebelumnanya 0, tiap mapping (looping) data
            ++$this->rowNumber,
            $cinema->name,
            $cinema->location,
        ];
    }
}
