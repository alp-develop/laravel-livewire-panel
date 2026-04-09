<?php

declare(strict_types=1);

namespace AlpDevelop\LivewirePanel\Modules\Auth\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PanelForgotPasswordNotification extends Notification
{
    public function __construct(
        protected readonly string $token,
        protected readonly string $resetUrl,
        protected readonly int $expireMinutes = 60,
    ) {}

    /** @return array<int, string> */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage())
            ->subject($this->emailSubject())
            ->view($this->emailView(), $this->emailData($notifiable));
    }

    protected function emailSubject(): string
    {
        return __('panel::messages.reset_password_title');
    }

    protected function emailView(): string
    {
        return 'panel::auth.emails.reset-password';
    }

    /** @return array<string, mixed> */
    protected function emailData(mixed $notifiable): array
    {
        return [
            'resetUrl'      => $this->resetUrl,
            'expireMinutes' => $this->expireMinutes,
        ];
    }
}
