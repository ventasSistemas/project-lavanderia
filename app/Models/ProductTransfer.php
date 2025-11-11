<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'complementary_product_id',
        'branch_id',
        'quantity',
        'status',
        'sent_by',
        'reviewed_by',
    ];

    public function product()
    {
        return $this->belongsTo(ComplementaryProduct::class, 'complementary_product_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
