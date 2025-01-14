<?php

namespace Esplora\Lumos;

use Esplora\Lumos\Contracts\SummaryInterface;
use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;

class Summary implements SummaryInterface
{
    /**
     * Success status of the extraction process.
     *
     * This flag indicates whether the extraction process has been successful.
     * If at least one step was successful, the process is considered successful.
     */
    protected bool $success = false;

    /**
     * Number of attempts made during the extraction process.
     *
     * This counts all attempts, including both successful and failed steps.
     */
    protected int $attempts = 0;

    /**
     * The password used in the extraction process, if any.
     *
     * This property stores the password associated with the last successful step.
     */
    protected ?string $password;

    /**
     * Steps involved in the extraction process.
     *
     * Contains all the steps of the process, including the success status and context for each step.
     */
    protected Collection $steps;

    /**
     * Constructor.
     *
     * Initializes an empty collection to store the steps of the extraction process.
     */
    public function __construct()
    {
        $this->steps = collect();
    }

    /**
     * Adds a step to the report.
     *
     * This method logs the result of the current step, including its success status and optional context.
     * Only unique steps are added to the collection.
     *
     * @param bool  $success The success status of the current step.
     * @param array $context The context of the current step, such as additional data or metadata.
     *
     * @return $this For method chaining.
     */
    public function addStep(bool $success, array $context = []): static
    {
        // If the current step is successful, mark the whole process as successful.
        if ($success) {
            $this->success = true;
            $this->password = $context['password'] ?? null;
        }

        // Increment the number of attempts.
        $this->attempts++;

        // Generate a unique hash for the step based on its success and context.
        $contextHash = $this->hashContext($success, $context);

        // Only add the step if it's not already in the collection (to avoid duplicates).
        if ($this->steps->has($contextHash)) {
            return $this;
        }

        // Add the step to the collection.
        $this->steps->put($contextHash, [
            'success' => $success,
            'context' => $context,
        ]);

        return $this;
    }

    /**
     * Generates a unique hash for a step.
     *
     * The hash is created based on the success status and the context of the step,
     * ensuring uniqueness and preventing duplicate steps from being added to the collection.
     *
     * @param bool  $success The success status of the step.
     * @param array $context The context of the step, containing additional information.
     *
     * @return string A unique hash for this step.
     */
    protected function hashContext(bool $success, array $context): string
    {
        $json = collect($context)
            ->except('password')
            ->toJson(JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return sha1($json);
    }

    /**
     * Retrieves all steps recorded in the report.
     *
     * This method returns a collection of all the steps that have been added during the process.
     * Each step includes its success status and associated context.
     *
     * @return Collection The collection of all steps in the report.
     */
    public function steps(): Collection
    {
        return $this->steps;
    }

    /**
     * Checks whether the entire process was successful.
     *
     * This method evaluates all steps and returns true if at least one step was successful.
     *
     * @return bool True if the process was successful, false otherwise.
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * Check if the entire process was unsuccessful.
     *
     * This method determines whether any step in the report has been unsuccessful.
     * It returns a boolean value indicating the overall failure status of the process.
     *
     * @return bool True if any step was unsuccessful, false otherwise.
     */
    public function isUnsuccessful(): bool
    {
        return ! $this->isSuccessful();
    }

    /**
     * Retrieves the total number of attempts.
     *
     * This method returns the total number of attempts, counting both successful and unsuccessful steps.
     *
     * @return int The total number of attempts made during the extraction process.
     */
    public function attempts(): int
    {
        return $this->attempts;
    }

    /**
     * Retrieves the password used in the extraction process.
     *
     * This method returns the password that was associated with the last successful step.
     * If no password was used or no successful steps were recorded, it returns null.
     *
     * @return string|null The password used, or null if not applicable.
     */
    public function password(): ?string
    {
        return $this->password;
    }

    /**
     * Adds a step with information about a process.
     *
     * This method logs a step with additional details from a Symfony Process object,
     * such as the success status, output, error output, exit code, and any associated password.
     *
     * @param bool        $success  The success status of the step.
     * @param Process     $process  The Symfony Process object containing process details.
     * @param string|null $password The password used in the extraction process, if any.
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
}
