<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Convert Persian numbers in CAPTCHA to English
        if ($request->has('captcha')) {
            $request->merge(['captcha' => fa2en($request->input('captcha'))]);
        }

        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message_text' => 'required|string|max:5000',
            'captcha' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $sessionCaptcha = Session::get('captcha_value');
                    if (!$sessionCaptcha || $value !== $sessionCaptcha) {
                        $fail(__('validation.captcha'));
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
        }
        
        Session::forget('captcha_value');

        $validatedData = $validator->validated();

        try {
            $contactMessage = ContactMessage::create([
                'name' => $validatedData['full_name'],
                'email' => $validatedData['email'],
                'subject' => $validatedData['subject'],
                'message' => $validatedData['message_text'],
            ]);

            // Send email notification
            // $adminEmail = config('mail.admin_email');
            // if ($adminEmail) {
            //     Mail::to($adminEmail)->send(new ContactMessageReceived($contactMessage));
            // } else {
            //     Log::warning('MAIN_ADMIN_EMAIL not set. Skipping contact message email notification.');
            // }

            return redirect()->back()->with('success', __('messages.contact.success_message'));

        } catch (\Exception $e) {
            Log::error('Error storing contact message: ' . $e->getMessage());
            return redirect()->back()->with('error', __('messages.contact.error_message'))->withInput();
        }
    }
}