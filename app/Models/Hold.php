<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Hold extends Model
{
    protected $fillable = ['product_id','qty','expires_at','used','released'];
    protected $casts = ['expires_at' => 'datetime', 'used' => 'boolean', 'released' => 'boolean'];

    public function product(){ return $this->belongsTo(Product::class); }

    // Mark used (order created)
    public function markUsed(): void
    {
        $this->used = true;
        $this->save();
    }

    // Release hold: add stock back and mark released
    public function release(): void
    {
        if ($this->released) return;
        $this->product->addStock($this->qty);
        $this->released = true;
        $this->save();
    }

    public function isActive(): bool
    {
        return !$this->used && !$this->released && $this->expires_at->isFuture();
    }
}
