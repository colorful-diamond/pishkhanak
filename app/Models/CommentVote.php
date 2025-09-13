<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'comment_id',
        'user_id',
        'ip_address',
        'vote_type',
    ];

    /**
     * Get the comment this vote belongs to
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(ServiceComment::class, 'comment_id');
    }

    /**
     * Get the user who cast this vote
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Create or update a vote
     */
    public static function castVote(int $commentId, string $voteType, ?int $userId = null, ?string $ipAddress = null): void
    {
        $comment = ServiceComment::findOrFail($commentId);
        
        // Find existing vote
        $existingVote = static::where('comment_id', $commentId)
            ->where(function ($query) use ($userId, $ipAddress) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($existingVote) {
            // If same vote type, remove the vote (toggle off)
            if ($existingVote->vote_type === $voteType) {
                // Decrement the appropriate counter
                if ($voteType === 'helpful') {
                    $comment->decrement('helpful_count');
                } else {
                    $comment->decrement('unhelpful_count');
                }
                
                $existingVote->delete();
                return;
            }

            // Change vote type
            $oldType = $existingVote->vote_type;
            $existingVote->update(['vote_type' => $voteType]);

            // Update counters
            if ($oldType === 'helpful') {
                $comment->decrement('helpful_count');
                $comment->increment('unhelpful_count');
            } else {
                $comment->decrement('unhelpful_count');
                $comment->increment('helpful_count');
            }
        } else {
            // Create new vote
            static::create([
                'comment_id' => $commentId,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
                'vote_type' => $voteType,
            ]);

            // Increment the appropriate counter
            if ($voteType === 'helpful') {
                $comment->increment('helpful_count');
            } else {
                $comment->increment('unhelpful_count');
            }
        }
    }
}