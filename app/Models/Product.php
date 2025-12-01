<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    protected $fillable = ['name','stock','price'];
    public function holds(){ return $this->hasMany(Hold::class); }

    // Atomically check & reduce stock using DB locking
    public function tryReserve(int $qty): bool
    {
        return DB::transaction(function () use ($qty) {
            $p = static::where('id', $this->id)->lockForUpdate()->first();
            if (!$p) return false;
            if ($p->stock < $qty) return false;
            $p->decrement('stock', $qty);
            return true;
        });
    }

    public function addStock(int $qty): void
    {
        $this->increment('stock', $qty);
    }
}
