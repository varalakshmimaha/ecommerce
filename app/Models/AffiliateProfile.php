<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AffiliateProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'address', 'city', 'state', 'pincode',
        'bank_name', 'account_number', 'ifsc', 'account_holder',
        'upi_id', 'pan_number', 'aadhaar_last4',
        'kyc_doc_path', 'kyc_verified', 'rejection_reason',
    ];

    protected $casts = [
        'kyc_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
