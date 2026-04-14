<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FineSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'denda_per_hari_terlambat',
        'persen_denda_rusak',
        'persen_denda_hilang',
    ];

    protected function casts(): array
    {
        return [
            'denda_per_hari_terlambat' => 'integer',
            'persen_denda_rusak' => 'decimal:2',
            'persen_denda_hilang' => 'decimal:2',
        ];
    }

    public static function active(): self
    {
        return self::query()->firstOrCreate([], [
            'denda_per_hari_terlambat' => 1000,
            'persen_denda_rusak' => 30,
            'persen_denda_hilang' => 100,
        ]);
    }
}
