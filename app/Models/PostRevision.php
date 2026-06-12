<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRevision extends Model
{
    use HasFactory;

    protected $fillable = ['post_id', 'user_id', 'revision_number', 'content', 'change_summary'];

    protected $casts = [
        'content' => 'json',
    ];

    /**
     * Get the post this revision belongs to.
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Get the user who made this revision.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the content data.
     */
    public function getContentData(): array
    {
        return $this->content ?? [];
    }
}
