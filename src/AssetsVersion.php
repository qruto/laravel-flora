<?php

namespace Qruto\Formula;

use Illuminate\Contracts\Cache\Repository;

class AssetsVersion
{
    protected ?string $latestHash;

    public function __construct(protected Repository $cache)
    {
        $this->latestHash = $this->cache->get('assets_hash');
    }

    public function outdated(): bool
    {
        $currentHash = $this->currentHash();
        $latestHash = $this->latestHash();

        if ($latestHash === null) {
            return true;
        }

        if ($currentHash === null) {
            return true;
        }

        return $latestHash !== $currentHash;
    }

    public function stampUpdate(): void
    {
        $this->cache->put('assets_hash', $this->currentHash());
    }

    protected function currentHash()
    {
        $composerLockPath = base_path('composer.lock');

        return file_exists($composerLockPath)
            ? json_decode(file_get_contents($composerLockPath), true, 512, JSON_THROW_ON_ERROR)['content-hash']
            : null;
    }

    protected function latestHash(): ?string
    {
        return $this->latestHash;
    }
}
