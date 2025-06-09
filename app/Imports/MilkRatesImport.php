<?php
namespace App\Imports;

use App\Models\MilkRate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MilkRatesImport implements ToModel, WithHeadingRow
{
    protected $adminId;

    public function __construct($adminId)
    {
        $this->adminId = $adminId;
    }

    public function model(array $row)
    {
        // Find existing record with same admin_id and fat
        $existing = MilkRate::where('admin_id', $this->adminId)
                            ->where('fat', $row['fat'] ?? null)
                            ->first();

        $data = [
            'admin_id'  => $this->adminId,
            'fat'       => $row['fat'] ?? null,
            'snf_7_5'   => $row['snf_7_5'] ?? null,
            'snf_7_6'   => $row['snf_7_6'] ?? null,
            'snf_7_7'   => $row['snf_7_7'] ?? null,
            'snf_7_8'   => $row['snf_7_8'] ?? null,
            'snf_7_9'   => $row['snf_7_9'] ?? null,
            'snf_8_0'   => $row['snf_8_0'] ?? null,
            'snf_8_1'   => $row['snf_8_1'] ?? null,
            'snf_8_2'   => $row['snf_8_2'] ?? null,
            'snf_8_3'   => $row['snf_8_3'] ?? null,
            'snf_8_4'   => $row['snf_8_4'] ?? null,
            'snf_8_5'   => $row['snf_8_5'] ?? null,
            'snf_8_6'   => $row['snf_8_6'] ?? null,
            'snf_8_7'   => $row['snf_8_7'] ?? null,
            'snf_8_8'   => $row['snf_8_8'] ?? null,
            'snf_8_9'   => $row['snf_8_9'] ?? null,
            'snf_9_0'   => $row['snf_9_0'] ?? null,
        ];

        if ($existing) {
            $existing->update($data);
            return null; // Skip creation since already updated
        }

        return new MilkRate($data);
    }
}

