<?php

/*
 * This file is part of the Acme PHP project.
 *
 * (c) Titouan Galopin <galopintitouan@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace InfinityFree\AcmeCore\Protocol;

/**
 * Represent a ACME challenge.
 *
 * @author Titouan Galopin <galopintitouan@gmail.com>
 */
class AuthorizationChallenge
{
    /** @var string */
    private $domain;

    /** @var string */
    private $status;

    /** @var string */
    private $type;

    /** @var string */
    private $url;

    /** @var string */
    private $token;

    /** @var string */
    private $payload;

    /** @var array */
    private $error;

    public function __construct(string $domain, string $status, string $type, string $url, string $token, string $payload, array $error = [])
    {
        $this->domain = $domain;
        $this->status = $status;
        $this->type = $type;
        $this->url = $url;
        $this->token = $token;
        $this->payload = $payload;
        $this->error = $error;
    }

    public function toArray(): array
    {
        return [
            'domain' => $this->getDomain(),
            'status' => $this->getStatus(),
            'type' => $this->getType(),
            'url' => $this->getUrl(),
            'token' => $this->getToken(),
            'payload' => $this->getPayload(),
            'error' => $this->getError(),
        ];
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['domain'],
            $data['status'],
            $data['type'],
            $data['url'],
            $data['token'],
            $data['payload'],
            $data['error']
        );
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function isValid(): bool
    {
        return 'valid' === $this->status;
    }

    public function isPending(): bool
    {
        return 'pending' === $this->status || 'processing' === $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getPayload(): string
    {
        return $this->payload;
    }

    public function getError(): array
    {
        return $this->error;
    }

    public function getErrorType(): string
    {
        return $this->error['type'] ?? '';
    }

    public function getErrorDetail(): string
    {
        return $this->error['detail'] ?? '';
    }

    public function getErrorStatus(): int
    {
        return $this->error['status'] ?? 0;
    }
}
