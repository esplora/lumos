<?php

namespace Esplora\Lumos\Contracts;

use Illuminate\Support\Collection;

interface SummaryInterface
{
    /**
     * Add a step to the report.
     *
     * Logs the result of a step in a process, including the success status and an optional context
     * with additional information about the step.
     *
     * @param bool  $success The success status of the current step.
     * @param array $context Optional context for the current step, containing additional data or metadata.
     *
     * @return static The current instance to allow method chaining.
     */
    public function addStep(bool $success, array $context = []): static;

    /**
     * Retrieve all steps of the report.
     *
     * Returns a collection of all the steps recorded in the report for further processing or analysis.
     *
     * @return Collection A collection of steps, each with success status and context data.
     */
    public function steps(): Collection;

    /**
     * Check if the entire process was successful.
     *
     * Determines whether all steps in the report were successful.
     *
     * @return bool True if all steps were successful, false otherwise.
     */
    public function isSuccessful(): bool;

    /**
     * Check if the entire process was unsuccessful.
     *
     * Determines whether any step in the report was unsuccessful.
     *
     * @return bool True if any step was unsuccessful, false otherwise.
     */
    public function isUnsuccessful(): bool;

    /**
     * Get the number of attempts made.
     *
     * Returns the total number of attempts recorded, which may include both successful and unsuccessful steps.
     *
     * @return int The total number of attempts.
     */
    public function attempts(): int;

    /**
     * Get the password used in the process.
     *
     * Returns the password associated with the process, if any.
     *
     * @return string|null The password or null if none was used.
     */
    public function password(): ?string;
}
