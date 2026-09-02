import { execSync } from 'node:child_process';

/**
 * Seed 4 E2E users via artisan tinker so loginAs() works even on fresh DB.
 * Runs before webServer boots in CI; with reuseExistingServer it still ensures users exist.
 */
export default async function globalSetup() {
  const users = [
    { name: 'E2E Root', email: 'e2e_rootsuperuser@dapense.test', usertype: 'rootsuperuser' },
    { name: 'E2E Admin', email: 'e2e_admin@dapense.test', usertype: 'admin' },
    { name: 'E2E Operator', email: 'e2e_operator@dapense.test', usertype: 'operator' },
    { name: 'E2E BOD', email: 'e2e_bod@dapense.test', usertype: 'bod' },
  ];

  for (const u of users) {
    // Use artisan command to upsert user; fallback to tinker if command not present.
    try {
      // Try custom command if exists, else use tinker with User::updateOrCreate
      const php = `User::updateOrCreate(['email'=>'${u.email}'], ['name'=>'${u.name}','password'=>Hash::make('password'),'usertype'=>'${u.usertype}','status'=>1]); \\Illuminate\\Support\\Facades\\DB::table('users')->where('email','${u.email}')->update(['email_verified_at'=>now()]); echo 'ok';`;
      execSync(`php artisan tinker --execute="use Illuminate\\Support\\Facades\\Hash; use App\\Models\\User; ${php}"`, { stdio: 'inherit', timeout: 15000 });
    } catch (e) {
      console.warn('[global-setup] seed failed for', u.email, e);
    }
  }
}
