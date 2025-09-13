<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceComment;
use App\Models\CommentVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ServiceCommentController extends Controller
{
    /**
     * Display comments for a service
     */
    public function index(Service $service)
    {
        $comments = $service->approvedComments()
            ->paginate(10);

        return response()->json([
            'comments' => $comments,
            'average_rating' => $service->average_rating,
            'total_ratings' => $service->total_ratings,
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Request $request, Service $service)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:10|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'parent_id' => 'nullable|exists:service_comments,id',
            'author_name' => 'required_without:user_id|string|max:100',
            'author_email' => 'required_without:user_id|email|max:100',
            'author_phone' => 'nullable|string|max:20',
        ], [
            'content.required' => 'لطفاً نظر خود را وارد کنید.',
            'content.min' => 'نظر شما باید حداقل ۱۰ کاراکتر باشد.',
            'content.max' => 'نظر شما نمی‌تواند بیشتر از ۱۰۰۰ کاراکتر باشد.',
            'rating.min' => 'امتیاز باید بین ۱ تا ۵ باشد.',
            'rating.max' => 'امتیاز باید بین ۱ تا ۵ باشد.',
            'author_name.required_without' => 'لطفاً نام خود را وارد کنید.',
            'author_email.required_without' => 'لطفاً ایمیل خود را وارد کنید.',
            'author_email.email' => 'لطفاً یک ایمیل معتبر وارد کنید.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if parent comment exists and belongs to the same service
        if ($request->parent_id) {
            $parentComment = ServiceComment::find($request->parent_id);
            if (!$parentComment || $parentComment->service_id !== $service->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'نظر مورد نظر یافت نشد.'
                ], 404);
            }
        }

        $comment = new ServiceComment();
        $comment->service_id = $service->id;
        $comment->user_id = Auth::id();
        $comment->parent_id = $request->parent_id;
        $comment->content = $request->content;
        $comment->rating = $request->parent_id ? null : $request->rating; // Only allow rating on parent comments
        $comment->author_name = Auth::check() ? null : $request->author_name;
        $comment->author_email = Auth::check() ? null : $request->author_email;
        $comment->author_phone = $request->author_phone;
        $comment->ip_address = $request->ip();
        $comment->user_agent = $request->userAgent();
        $comment->status = 'pending'; // Comments need approval
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'نظر شما با موفقیت ثبت شد و پس از بررسی منتشر خواهد شد.',
            'comment' => $comment
        ]);
    }

    /**
     * Vote on a comment
     */
    public function vote(Request $request, ServiceComment $comment)
    {
        $validator = Validator::make($request->all(), [
            'vote_type' => 'required|in:helpful,unhelpful',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if comment is approved
        if ($comment->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'این نظر هنوز تایید نشده است.'
            ], 403);
        }

        $userId = Auth::id();
        $ipAddress = $request->ip();

        // Check if user has already voted
        if ($comment->hasUserVoted($userId, $ipAddress)) {
            $existingVoteType = $comment->getUserVoteType($userId, $ipAddress);
            
            if ($existingVoteType === $request->vote_type) {
                // Remove vote if clicking the same button
                CommentVote::castVote($comment->id, $request->vote_type, $userId, $ipAddress);
                
                return response()->json([
                    'success' => true,
                    'message' => 'رای شما حذف شد.',
                    'helpful_count' => $comment->fresh()->helpful_count,
                    'unhelpful_count' => $comment->fresh()->unhelpful_count,
                ]);
            }
        }

        // Cast or update vote
        CommentVote::castVote($comment->id, $request->vote_type, $userId, $ipAddress);

        return response()->json([
            'success' => true,
            'message' => 'رای شما با موفقیت ثبت شد.',
            'helpful_count' => $comment->fresh()->helpful_count,
            'unhelpful_count' => $comment->fresh()->unhelpful_count,
        ]);
    }

    /**
     * Report a comment as inappropriate
     */
    public function report(Request $request, ServiceComment $comment)
    {
        $validator = Validator::make($request->all(), [
            'reason' => 'required|string|max:500',
        ], [
            'reason.required' => 'لطفاً دلیل گزارش را وارد کنید.',
            'reason.max' => 'دلیل گزارش نمی‌تواند بیشتر از ۵۰۰ کاراکتر باشد.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Here you could implement a reporting system
        // For now, we'll just mark it for review
        $comment->status = 'pending';
        $comment->rejection_reason = 'گزارش شده: ' . $request->reason;
        $comment->save();

        return response()->json([
            'success' => true,
            'message' => 'گزارش شما با موفقیت ثبت شد و بررسی خواهد شد.',
        ]);
    }

    /**
     * Load more replies for a comment
     */
    public function replies(ServiceComment $comment)
    {
        if ($comment->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'نظر مورد نظر یافت نشد.'
            ], 404);
        }

        $replies = $comment->approvedReplies()->get();

        return response()->json([
            'success' => true,
            'replies' => $replies,
        ]);
    }
}