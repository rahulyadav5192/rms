<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VisaDetail extends Model
{
    use HasCompany;

    const FILE_PATH = 'visa';
    // protected $table = 'passport';
    protected $appends = ['image_url'];
    protected $dates = ['issue_date', 'expiry_date'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getImageUrlAttribute()
    {
        return asset_url(VisaDetail::FILE_PATH . '/'  . $this->file);
    }

    public function country(): HasOne
    {
        return $this->hasOne(Country::class, 'id', 'country_id');
    }

}
