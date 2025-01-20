<?php

namespace Esplora\Lumos;

use Esplora\Lumos\Contracts\AdapterInterface;
use Esplora\Lumos\Contracts\PasswordProviderInterface;
use Esplora\Lumos\Contracts\SummaryInterface;
use Esplora\Lumos\Providers\ArrayPasswordProvider;
use Illuminate\Support\Collection;

/**
 * Class for extracting files with support for passwords and file adapters.
 *
 * This class manages the extraction process, supporting various file types through adapters and
 * using different passwords via a password provider.
 */
class Extractor
{
    /**
     * Password provider for handling protected files.
     */
    protected PasswordProviderInterface $passwordProvider;

    /**
     * Collection of adapters adapters for extracting files.
     *
     * @var \Illuminate\Support\Collection<AdapterInterface>
     */
    protected Collection $adapters;

    /**
     * Constructor for the Extractor class.
     *
     * Initializes default adapters for successful and failed extractions.
     */
    public function __construct(iterable $adapters = [])
    {
        // Default password provider with an empty password list.
        $this->passwordProvider = new ArrayPasswordProvider([]);

        $this->adapters = collect($adapters);
    }

    /**
     * Short hand method to create an Extractor instance with the provided files adapters.
     *
     * @param iterable $adapters iterable of file adapters.
     */
    public static function make(iterable $adapters = []): static
    {
        return (new static)->withAdapters($adapters);
    }

    /**
     * Sets the password provider for handling protected files.
     *
     * @param PasswordProviderInterface $provider The password provider to use.
     *
     * @return $this For method chaining.
     */
    public function withPasswords(PasswordProviderInterface $provider): static
    {
        $this->passwordProvider = $provider;

        return $this;
    }

    /**
     * Adds an file adapter to support different file formats.
     *
     * @param AdapterInterface $adapter The file adapter.
     *
     * @return $this For method chaining.
     */
    public function withAdapter(AdapterInterface $adapter): static
    {
        $this->adapters->push($adapter);

        return $this;
    }

    /**
     * Adds multiple file adapters.
     *
     * @param AdapterInterface[] $adapters Array of file adapters.
     *
     * @return $this For method chaining.
     */
    public function withAdapters(iterable $adapters): static
    {
        $this->adapters = $this->adapters->merge($adapters);

        return $this;
    }

    /**
     * Extracts the file to the specified location.
     *
     * This method performs the extraction and handles exceptions and callbacks for failure or success.
     *
     * @param string      $filePath    Path to the file.
     * @param string|null $destination Directory to extract to. If not specified, uses the same directory as the file.
     *
     * @throws \RuntimeException If the password provider is not set.
     *
     * @return SummaryInterface Result of the success callback or password failure callback.
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
     * @return Collection<AdapterInterface>
     */
    public function getSupportedAdapters(string $filePath): Collection
    {
        return $this->adapters
            ->filter(fn (AdapterInterface $adapter) => $adapter->canSupport($filePath));
    }
}
