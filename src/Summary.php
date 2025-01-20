<?php

namespace Esplora\Lumos;

use Esplora\Lumos\Contracts\SummaryInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class Summary implements SummaryInterface
{
    /**
     * Indicates if the extraction process was successful.
     */
    protected bool $success = false;

    /**
     * Total number of attempts made.
     */
    protected int $attempts = 0;

    /**
     * Password used in the last successful step, if any.
     */
    protected ?string $password = null;

    /**
     * Steps of the extraction process.
     */
    protected Collection $steps;

    /**
     * Create a new summary instance.
     */
    public function __construct()
    {
        $this->steps = collect();
    }

    /**
     * Get all recorded steps.
     */
    public function steps(): Collection
    {
        return $this->steps;
    }

    /**
     * Determine if the process was unsuccessful.
     */
    public function isUnsuccessful(): bool
    {
        return ! $this->isSuccessful();
    }

    /**
     * Determine if the process was successful.
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Get the total number of attempts.
     */
    public function attempts(): int
    {
        return $this->attempts;
    }

    /**
     * Get the password used in the extraction process.
     */
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * Add a step with Symfony Process details.
     *
     * @param bool        $success  Success status of the step.
     * @param Process     $process  Symfony Process instance.
     * @param string|null $password Associated password, if any.
     *
     * @return $this For method chaining.
     */
    public function addStepWithProcess(bool $success, Process $process, ?string $password = null): static
    {
        return $this->addStep($success, [
            'isSuccessful' => $process->isSuccessful(),
            'output'       => $process->getOutput(),
            'error'        => $process->getErrorOutput(),
            'exitCode'     => $process->getExitCode(),
            'exitCodeText' => $process->getExitCodeText(),
            'password'     => $password,
        ]);
    }

    /**
     * Add a step to the process.
     *
     * @param bool  $success The success status of the step.
     * @param array $context Additional data or metadata for the step.
     *
     * @return $this For method chaining.
     */
    public function addStep(bool $success, array $context = []): static
    {
        if ($success) {
            $this->success = true;
            $this->password = $context['password'] ?? null;
        }

        $this->attempts++;
        $contextHash = $this->hashContext($success, $context);

        if ($this->steps->has($contextHash)) {
            return $this;
        }

        $this->steps->put($contextHash, [
            'success' => $success,
            'context' => $context,
        ]);

        return $this;
    }

    /**
     * Generate a unique hash for a step's context.
     */
    protected function hashContext(bool $success, array $context): string
    {
        $json = collect($context)
            ->except('password')
            ->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return sha1($json);
    }
}
