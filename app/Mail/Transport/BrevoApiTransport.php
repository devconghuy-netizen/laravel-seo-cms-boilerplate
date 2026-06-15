<?php

namespace App\Mail\Transport;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(private readonly ?string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('Brevo API transport only supports Symfony Email messages.');
        }

        if (! $this->apiKey) {
            throw new TransportException('Missing BREVO_API_KEY for Brevo API mail transport.');
        }

        $payload = array_filter([
            'sender' => $this->address($email->getFrom()[0] ?? null),
            'to' => $this->addresses($email->getTo()),
            'cc' => $this->addresses($email->getCc()),
            'bcc' => $this->addresses($email->getBcc()),
            'replyTo' => $this->address($email->getReplyTo()[0] ?? null),
            'subject' => $email->getSubject(),
            'htmlContent' => $email->getHtmlBody(),
            'textContent' => $email->getTextBody(),
        ], fn ($value) => filled($value));

        $response = Http::withHeaders([
            'api-key' => $this->apiKey,
            'accept' => 'application/json',
        ])
            ->asJson()
            ->timeout(20)
            ->post('https://api.brevo.com/v3/smtp/email', $payload);

        if ($response->failed()) {
            throw new TransportException(sprintf(
                'Brevo API mail send failed with status %s: %s',
                $response->status(),
                $response->body()
            ));
        }
    }

    public function __toString(): string
    {
        return 'brevo-api';
    }

    /**
     * @param  Address[]  $addresses
     * @return array<int, array{email: string, name?: string}>
     */
    private function addresses(array $addresses): array
    {
        return array_values(array_filter(array_map($this->address(...), $addresses)));
    }

    /**
     * @return array{email: string, name?: string}|null
     */
    private function address(?Address $address): ?array
    {
        if (! $address) {
            return null;
        }

        return array_filter([
            'email' => $address->getAddress(),
            'name' => $address->getName(),
        ], fn ($value) => filled($value));
    }
}
