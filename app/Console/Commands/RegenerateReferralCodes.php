<?php

namespace App\Console\Commands;

use Core\Users\Models\User;
use Illuminate\Console\Command;

class RegenerateReferralCodes extends Command
{
    protected $signature = 'users:referral-codes
                            {--user= : Specific user ID to regenerate code for}
                            {--ambiguous-only : Regenerate only codes containing ambiguous characters (0, O, 1, I, L) or lowercase}
                            {--all : Regenerate referral codes for all users}
                            {--dry-run : Preview changes without saving}';

    protected $description = 'Regenerate user referral codes using unambiguous 6-character uppercase format (e.g. KX7EYG)';

    public function handle(): int
    {
        $userId = $this->option('user');
        $ambiguousOnly = $this->option('ambiguous-only');
        $all = $this->option('all');
        $dryRun = $this->option('dry-run');

        if (!$userId && !$ambiguousOnly && !$all) {
            $this->warn('Please specify an option: --user=ID, --ambiguous-only, or --all (combine with --dry-run to preview).');
            return 1;
        }

        $query = User::query();

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->get();

        $updated = 0;
        $skipped = 0;

        foreach ($users as $user) {
            $currentCode = $user->referral_code;

            $isAmbiguous = empty($currentCode) ||
                strlen($currentCode) !== 6 ||
                preg_match('/[01ioOIlL]/', $currentCode) ||
                preg_match('/[^23456789ABCDEFGHJKMNPQRSTUVWXYZ]/', $currentCode);

            if ($ambiguousOnly && !$isAmbiguous) {
                $skipped++;
                continue;
            }

            $newCode = generate_referral_code(6);

            $this->line(sprintf(
                'User #%d (%s): "%s" -> "%s"%s',
                $user->id,
                $user->fullname ?? 'No Name',
                $currentCode ?? 'NULL',
                $newCode,
                $dryRun ? ' (dry-run)' : ''
            ));

            if (!$dryRun) {
                $user->referral_code = $newCode;
                $user->saveQuietly();
            }

            $updated++;
        }

        $this->info(sprintf(
            'Completed. %d codes %s, %d skipped.',
            $updated,
            $dryRun ? 'simulated' : 'updated',
            $skipped
        ));

        return 0;
    }
}
