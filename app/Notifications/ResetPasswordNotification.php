<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * Membuat email pengaturan ulang password dengan teks resmi sekolah.
     */
    public function toMail($notifiable): MailMessage
    {
        $name = trim((string) $notifiable->name);
        $expiration = config(
            'auth.passwords.'.config('auth.defaults.passwords').'.expire'
        );

        return (new MailMessage)
            ->subject('Atur Ulang Password Website Sekolah')
            ->greeting($name !== '' ? "Halo, {$name}!" : 'Halo!')
            ->line('Kami menerima permintaan untuk mengatur ulang password akun Anda pada website SD Muhammadiyah Komplek Kolombo.')
            ->action('Atur Ulang Password', $this->resetUrl($notifiable))
            ->line("Tautan pengaturan ulang password ini hanya berlaku selama {$expiration} menit demi menjaga keamanan akun Anda.")
            ->line('Jika Anda tidak merasa meminta pengaturan ulang password, abaikan email ini. Password akun Anda tidak akan berubah.')
            ->salutation("Salam,\nSD Muhammadiyah Komplek Kolombo");
    }
}
