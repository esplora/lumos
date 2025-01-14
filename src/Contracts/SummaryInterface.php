<?php

namespace Esplora\Lumos\Contracts;

use Illuminate\Support\Collection;

interface SummaryInterface
{
    /**
     * Add a step to the report.
     *
     * This method allows you to log the result of a step in a process,
     * including the success status and an optional context that may contain
     * additional information about the step.
     *
     * @param bool  $success The success status of the current step.
     * @param array $context Optional context for the current step, such as
     *                       additional data or metadata related to the step.
     *
     * @return static The current instance to allow method chaining.
     */
    public function addStep(bool $success, array $context = []): static;

    /**
     * Retrieve all steps of the report.
     *
     * This method returns a collection of all the steps recorded in the report,
     * which can be further processed or analyzed.
     *
     * @return Collection A collection of steps, each containing success status
     *                    and context data.
     */
    public function steps(): Collection;

    /**
     * Check if the entire process was successful.
     *
     * This method determines whether all steps in the report have been successful.
     * It returns a boolean value indicating the overall success status of the process.
     *
     * @return bool True if all steps were successful, false otherwise.
     */
    public function isSuccessful(): bool;

    /**
     * Check if the entire process was unsuccessful.
     *
     * This method determines whether any step in the report has been unsuccessful.
     * It returns a boolean value indicating the overall failure status of the process.
     *
     * @return bool True if any step was unsuccessful, false otherwise.
     */
    public function isUnsuccessful(): bool;

    /**
     * Get the number of attempts made.
     *
     * This method returns the total number of attempts recorded in the report,
     * which may include both successful and unsuccessful steps.
     *
     * @return int The total number of attempts recorded.
     */
    public function attempts(): int;

    /**
     * Get the password used in the process.
     *
     * This method returns the password associated with the process, if any.
     *
     * @return string|null The password or null if none was used.
     */
    public function password(): ?string;
}
