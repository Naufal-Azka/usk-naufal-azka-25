<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnBook extends Model
{
    use HasFactory;

    protected $table = 'return_books';

    protected $fillable = [
        'borrowing_id',
        'admin_id',
        'tanggal_dikembalikan',
        'terlambat_hari',
        'denda',
        'kondisi_buku',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_dikembalikan' => 'date',
            'terlambat_hari' => 'integer',
            'denda' => 'decimal:2',
        ];
    }

    public function borrowing(): BelongsTo
    {
        return $this->belongsTo(Borrowing::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}