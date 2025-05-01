<?php

namespace App\Models;

use App\Enum\TableStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'table_number',
        'qr_code',
        'seating_capacity',
        'location',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => TableStatus::class,
        'seating_capacity' => 'integer',
    ];

    /**
     * Get the branch that owns the table.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the orders for this table.
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope a query to only include available tables.
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', TableStatus::AVAILABLE);
    }

    /**
     * Scope a query to only include occupied tables.
     */
    public function scopeOccupied($query)
    {
        return $query->where('status', TableStatus::OCCUPIED);
    }

    /**
     * Scope a query to filter tables by branch.
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to filter tables by seating capacity.
     */
    public function scopeByCapacity($query, $capacity)
    {
        return $query->where('seating_capacity', '>=', $capacity);
    }
}
