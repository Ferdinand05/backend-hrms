<?php

namespace App\Jobs;

use App\Mail\PayrollPaidMail;
use App\Models\Payroll;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class UpdatePayrollToPaid implements ShouldQueue
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


        $payrolls = Payroll::whereIn('id', $this->ids)
            ->with(['employee', 'employee.user', 'salary'])
            ->get();

        // Update status
        Payroll::whereIn('id', $this->ids)->update(['status' => 'paid']);

        foreach ($payrolls as $payroll) {
            $user = $payroll->employee?->user;

            if ($user && $user->email) {
                try {
                    Mail::to($user->email)
                        ->queue(new PayrollPaidMail($payroll));
                    Log::info("PayrollPaidMail sent to {$user->email} for payroll id {$payroll->id}");
                } catch (\Throwable $e) {
                    Log::error("Failed to send PayrollPaidMail to {$user->email} for payroll id {$payroll->id}: " . $e->getMessage());
                    // optionally: throw $e to let job retry / go to failed_jobs
                }
            } else {
                Log::warning("No email user for payroll id {$payroll->id}");
            }
        }
    }
}
