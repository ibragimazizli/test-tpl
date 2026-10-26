<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $table = 'articles';

    public const CATEGORY_INSURANCE = 7;
    public const STATUS_ACTIVE = 1;

    protected $fillable = [
        'category_id',
        'status_in_tpl',
        'tin',
        'file',
        'email',
        'phone',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(ArticleTranslation::class);
    }
}
