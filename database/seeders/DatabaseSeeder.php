<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // Assuming User model exists
use App\Models\Project; // Assuming Project model exists
use App\Models\Task; // Assuming Task model exists
use App\Models\Account; // Assuming Account model exists

/**
 * The main database seeder class for the SEMOP Magic System.
 * This seeder orchestrates the execution of all other seeders to populate the database with demo data.
 *
 * @package Database\Seeders
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * This method calls individual seeders to create:
     * 1. 10 Users
     * 2. 5 Projects
     * 3. 20 Tasks
     * 4. Sample Accounts
     *
     * @return void
     */
    public function run(): void
    {
        // Disable foreign key checks before seeding to prevent issues with truncated tables
        // and re-enable them afterwards. This is a common practice in seeding.
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Seed Users (10 users)
        // This seeder is responsible for creating 10 demo users.
        $this->call(UserSeeder::class);

        // 2. Seed Projects (5 projects)
        // This seeder is responsible for creating 5 demo projects, potentially assigning them to the seeded users.
        $this->call(ProjectSeeder::class);

        // 3. Seed Tasks (20 tasks)
        // This seeder is responsible for creating 20 demo tasks, linking them to the seeded projects and users.
        $this->call(TaskSeeder::class);

        // 4. Seed Accounts (Sample accounts)
        // This seeder is responsible for creating sample financial accounts or related data.
        $this->call(AccountSeeder::class);

        // 5. Seed Organizational Structure (الهيكل التنظيمي)
        // Seeders for Holdings, Units, Departments, and Projects
        $this->command->info('\n📊 بدء تشغيل seeders الهيكل التنظيمي...');
        $this->call([
            HoldingSeeder::class,
            UnitSeeder::class,
            DepartmentSeeder::class,
            \Database\Seeders\ProjectSeeder::class, // Organizational projects
        ]);
        $this->command->info('✅ تم الانتهاء من الهيكل التنظيمي (3 شركات + 12 وحدة + 18 قسم + 25 مشروع)\n');

        // Re-enable foreign key checks
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Optional: Create a default admin user for easy login
        if (!User::where('email', 'admin@semop.com')->exists()) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@semop.com',
                'password' => Hash::make('password'), // Use a secure default password
                'email_verified_at' => now(),
            ]);
        }

        // Output a message to the console for confirmation
        $this->command->info('Database seeded successfully with demo data (10 Users, 5 Projects, 20 Tasks, Sample Accounts).');
    }
}
