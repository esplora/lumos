<?php

namespace Esplora\Lumos;

use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Contracts\PasswordProviderInterface;
use Esplora\Lumos\Contracts\SummaryInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;
use Illuminate\Support\Collection;

class Extractor
{
    /**
     * Password provider for handling protected files.
     */
    protected PasswordProviderInterface $passwordProvider;

    /**
     * Collection of adapters for extracting files.
     *
     * @var \Illuminate\Support\Collection<AdapterInterface>
     */
    protected Collection $adapters;

    /**
     * Constructor for the Extractor class.
     *
     * Initializes with default adapters and an empty password provider.
     */
    public function __construct(iterable $adapters = [])
    {
        $this->passwordProvider = new ArrayPasswordProvider([]);
        $this->adapters = collect($adapters);
    }

    /**
     * Short hand method to create an Extractor instance with provided adapters.
     *
     * @param iterable $adapters Array of file adapters.
     *
     * @return static
     */
    public static function make(iterable $adapters = []): static
    {
        return new static($adapters);
    }

    /**
     * Sets the password provider for handling protected files.
     *
     * @param PasswordProviderInterface $provider The password provider.
     *
     * @return $this
     */
    public function withPasswords(PasswordProviderInterface $provider): static
    {
        $this->passwordProvider = $provider;

        return $this;
    }

    /**
     * Adds a file adapter to the extractor.
     *
     * @param AdapterInterface $adapter The file adapter.
     *
     * @return $this
     */
    public function withAdapter(AdapterInterface $adapter): static
    {
        return $this->withAdapters([$adapter]);
    }

    /**
     * Adds multiple file adapters.
     *
     * @param iterable<AdapterInterface> $adapters Array of file adapters.
     *
     * @return $this
     */
    public function withAdapters(iterable $adapters): static
    {
        $this->adapters = $this->adapters->merge($adapters);

        return $this;
    }

    /**
     * Extracts the file to the specified location.
     *
     * @param string      $filePath    Path to the file.
     * @param string|null $destination Directory to extract to (default is the file's directory).
     *
     * @throws \RuntimeException If no adapter is found for the given file.
     *
     * @return SummaryInterface Result of extraction process.
     */
    public function extract(string $filePath, ?string $destination = null): SummaryInterface
    {
        $destination = $destination ?: dirname($filePath);

        /** @var AdapterInterface $adapter */
        $adapter = $this
            ->getSupportedAdapters($filePath)
            ->whenEmpty(fn () => throw new \RuntimeException("No adapter found for file: {$filePath}"))
            ->first();

        return $adapter->extract($filePath, $destination, $this->passwordProvider);
    }

    /**
     * Retrieves supported adapters for the given file path.
     *
     * @param string $filePath Path to the file.
     *
     * @return Collection<AdapterInterface>
     */
    public function getSupportedAdapters(string $filePath): Collection
    {
        return $this->adapters->filter(fn (AdapterInterface $adapter) => $adapter->canSupport($filePath));
    }
}
