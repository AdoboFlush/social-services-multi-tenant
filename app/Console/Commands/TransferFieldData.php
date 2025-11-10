<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class TransferFieldData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transfer:field-data {table : The name of the table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Transfer a value from one column to another based on a search value';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Get the table name from the argument
        $table = $this->argument('table');

        // Get user inputs
        $searchValue = $this->ask('Enter the value to search for:');
        $transferColumn = $this->ask('Enter the name of the column to transfer the value from (e.g., `old_field`):');
        $targetColumn = $this->ask('Enter the name of the column to transfer the value to (e.g., `new_field`):');

        // Check if the table exists
        if (!DB::getSchemaBuilder()->hasTable($table)) {
            $this->error("Table '{$table}' does not exist.");
            return 1;
        }

        // Check if the fields exist in the table
        $columns = DB::getSchemaBuilder()->getColumnListing($table);
        $missingColumns = array_diff([$transferColumn, $targetColumn], $columns);
        if (!empty($missingColumns)) {
            $this->error('The following columns are missing: ' . implode(', ', $missingColumns));
            return 1;
        }

        // Count rows matching the criteria
        $rowsToUpdate = DB::table($table)
            ->where($transferColumn, $searchValue)
            ->count();

        // Show summary and confirm
        $this->info("Summary of changes:");
        $this->info("- Table: {$table}");
        $this->info("- Search Value: {$searchValue}");
        $this->info("- Transfer From: {$transferColumn}");
        $this->info("- Transfer To: {$targetColumn}");
        $this->info("- Rows to be updated: {$rowsToUpdate}");

        if (!$this->confirm('Do you want to proceed with these changes?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        // Perform the update
        try {
            $affectedRows = DB::table($table)
                ->where($transferColumn, $searchValue)
                ->update([
                    $targetColumn => DB::raw($transferColumn),
                    $transferColumn => ''
                ]);

            if ($affectedRows > 0) {
                $this->info("Data successfully transferred for {$affectedRows} row(s).");
            } else {
                $this->warn("No rows matched the criteria.");
            }
        } catch (\Exception $e) {
            $this->error('Error transferring data: ' . $e->getMessage());
        }

        return 0;
    }
}
