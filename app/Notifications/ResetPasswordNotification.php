<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $code;

    public function __construct($code)
    {
        $this->code = $code;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('إعادة تعيين كلمة المرور')
            ->line('لقد طلبت إعادة تعيين كلمة المرور الخاصة بك.')
            ->line('كود التحقق الخاص بك هو: ' . $this->code)
            ->line('شكراً لاستخدامك تطبيقنا!');
    }
}