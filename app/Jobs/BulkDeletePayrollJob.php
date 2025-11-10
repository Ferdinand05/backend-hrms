<?php

namespace App\Jobs;

use App\Models\Payroll;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BulkDeletePayrollJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected array $ids;
    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $payroll =  Payroll::whereIn('id', $this->ids)
            ->with(['employee', 'salary'])
            ->delete();
    }
}
