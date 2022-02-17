<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;

class LPUsersImport implements ToCollection, WithChunkReading, ShouldQueue,WithStartRow
{
    public function collection(Collection $rows)
    {
         info("rows collection");
         info($rows);  
    }

     public function startRow(): int 
    {
         return 1;
    }

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}