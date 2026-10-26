<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleTranslation extends Model
{
    use HasFactory;

    protected $table = 'article_translations';

    protected $fillable = [
        'article_id',
        'lang',
        'title',
        'link',
        'address',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
