<?php

namespace Qruto\Initializer;

use Illuminate\Contracts\Cache\Repository;

// TODO: get hash with cache:clear command
class AssetsVersion
{
    protected ?string $latestHash;

    public function __construct(protected Repository $cache)
    {
        $this->latestHash = $this->cache->get('assets_hash');
    }

    public function outdated(): bool
    {
        if (! file_exists(base_path('composer.lock'))) {
            return true;
        }

        $currentHash = $this->currentHash();
        $latestHash = $this->latestHash();

        if ($latestHash === null || $currentHash === null) {
            return true;
        }

        if ($latestHash !== $currentHash) {
            return true;
        }

        return false;
    }

    public function stampUpdate(): void
    {
        $this->cache->put('assets_hash', $this->currentHash());
    }

    protected function currentHash()
    {
        $composerLockPath = base_path('composer.lock');

        return file_exists($composerLockPath)
            ? json_decode(file_get_contents($composerLockPath), true)['content-hash']
            : null;
    }

    protected function latestHash(): ?string
    {
        return $this->latestHash;
    }
}
