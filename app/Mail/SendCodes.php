<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCodes extends Mailable
{
    use Queueable, SerializesModels;

    private $name;
    private $code;
    private $code2;
    private $book;
    private $editorial;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $code, $code2, $book, $editorial)
    {
        $this->name = $name;
        $this->code = $code;
        $this->code2 = $code2;
        $this->book = $book;
        $this->editorial = $editorial;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('registro.pagos@majesticeducacion.com.mx')
            ->subject(__("Codigo"))
            ->markdown('mails.send-code')
            ->with('name', $this->name)
            ->with('code', $this->code)
            ->with('code2', $this->code2)
            ->with('book', $this->book)
            ->with('editorial', $this->editorial);
    }
}
