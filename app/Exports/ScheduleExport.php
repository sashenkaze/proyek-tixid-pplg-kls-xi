<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class ScheduleExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    private $rowNumber = 0;
    public function collection()
    {
        return Schedule::with(['cinema', 'movie'])->get();
    }

    public function headings():array
    {
        return ['No', 'Bioskop', 'Film', 'Harga', 'Jam Tayang'];
    }

    public function map($schedule): array
    {
        $schedule->price = 'Rp ' . number_format($schedule->price, 0, ',', '.');
        return [
            //increment rownumber yang sebelumnanya 0, tiap mapping (looping) data
            ++$this->rowNumber,
            $schedule->cinema['name'],
            $schedule->movie['title'],
            $schedule->price,
            array_merge($schedule->hours),

        ];
    }
}
