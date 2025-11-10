<?php

namespace App\Console\Commands;

use App\AssistanceEvent;
use App\VoterTagDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class GenerateAssistanceQRCoupons extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    // delimeter ';' . custom_query example: "affiliation_1=Test;party_list=ASAP"
    protected $signature = 'generate:qr-coupons {assistance_event_id} {custom_query?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'generate qr-coupons for each assistance event';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start_time = microtime(true); // Start benchmark

        // Ensure the storage directory exists
        $storage_path = storage_path();
        if (!File::exists($storage_path)) {
            File::makeDirectory($storage_path, 0755, true);
        }

        $assistance_event_id = $this->argument('assistance_event_id');
        $custom_query = $this->argument('custom_query');

        $assistance_event = AssistanceEvent::find($assistance_event_id);
        if ($assistance_event) {

            $voters = new VoterTagDetail;

            if(!empty($custom_query)) {
                $custom_query_params = explode(";", $custom_query);
                foreach($custom_query_params as $custom_query_param) {
                    $param_part = explode("=", $custom_query_param);
                    if(isset($param_part[0]) && isset($param_part[1])) {
                        $voters = $voters->where(trim($param_part[0]), trim($param_part[1]));
                    }
                }
            }

            $voters = $voters->limit(10); // test

            $total_voters = $voters->count(); // Get total voters for progress bar
            $voters = $voters->cursor();
            $progress_bar = $this->output->createProgressBar($total_voters); // Initialize progress bar
            $progress_bar->start();

            $cards_per_page = 10;
            $card_width = 1012;
            $card_height = 637;
            $a4_width = 2480;
            $a4_height = 3508;
            $margin = 50;
            $cards = [];
            $page_count = 0;

            // Determine the correct command based on the operating system
            $is_windows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

            $command_prefix = function ($command) use ($is_windows) {
                if($is_windows) {
                    return $command == 'composite' ? "magick {$command}" : 'magick';
                }
                return "/usr/bin/{$command}";
            };

            foreach ($voters as $voter) {
                $qr_file_name = "qrcode-{$voter->id}.png";
                $qr_path = storage_path($qr_file_name);
                $qr_data = Crypt::encryptString("{$assistance_event->id}|{$voter->id}");
                QrCode::format('png')->size(300)->generate($qr_data, $qr_path);

                $card_file_name = "card-{$voter->id}.png";
                $card_path = storage_path($card_file_name);
                $background_color = 'white';

                exec($command_prefix('convert')." -size {$card_width}x{$card_height} xc:{$background_color} {$card_path}", $output, $return_var);
                if ($return_var !== 0) {
                    $this->error("Failed to create card image for voter ID {$voter->id}");
                    $progress_bar->advance(); // Advance progress bar even if there's an error
                    continue;
                }

                $qr_x = ($card_width - 300) / 2;
                $qr_y = 50;
                exec($command_prefix('composite')." -geometry +{$qr_x}+{$qr_y} {$qr_path} {$card_path} {$card_path}", $output, $return_var);
                if ($return_var !== 0) {
                    $this->error("Failed to overlay QR code for voter ID {$voter->id}");
                    $progress_bar->advance(); // Advance progress bar even if there's an error
                    continue;
                }

                // Add voter annotations below the QR code
                $font = 'Arial'; // Ensure the font is installed on the system
                $font_size = 24;
                $text_color = 'black';

                $full_name = $voter->full_name;
                $brgy = $voter->brgy;
                $birth_date = $voter->birth_date;

                $annotation_y = $qr_y + 400 + 20; // Start below the QR code with some padding
                
                // Explicitly specify the output format and use double quotes for text
                exec($command_prefix('convert')." {$card_path} -font {$font} -pointsize {$font_size} -fill {$text_color} -annotate +50+{$annotation_y} \"{$full_name} | {$brgy} | {$birth_date} \" {$card_path}");

                // Optionally, delete the temporary QR code file
                unlink($qr_path);

                $cards[] = $card_path;

                if (count($cards) === $cards_per_page) {
                    $page_count++;
                    $this->generateA4Page($cards, $page_count, $card_width, $card_height, $a4_width, $a4_height, $margin, $command_prefix);
                    $cards = [];
                }

                $progress_bar->advance(); // Advance progress bar after processing each voter
            }

            if (!empty($cards)) {
                $page_count++;
                $this->generateA4Page($cards, $page_count, $card_width, $card_height, $a4_width, $a4_height, $margin, $command_prefix);
            }

            $progress_bar->finish(); // Finish the progress bar
            $this->info("\nQR coupon generation completed.");

            // Add zip functionality for Linux
            if (!$is_windows) {
                $zip_file_name = "a4-pages.zip";
                $zip_path = storage_path($zip_file_name);

                $a4_files = glob(storage_path("a4-page-*.png"));
                $a4_files_list = implode(' ', array_map('escapeshellarg', $a4_files));

                exec("zip -j {$zip_path} {$a4_files_list}", $output, $return_var);
                if ($return_var === 0) {
                    $this->info("A4 pages have been zipped into: {$zip_file_name}");
                    // Optionally, delete the individual A4 files after zipping
                    foreach ($a4_files as $file) {
                        unlink($file);
                    }
                } else {
                    $this->error("Failed to create zip file for A4 pages.");
                }
            }

            $end_time = microtime(true); // End benchmark
            $execution_time = $end_time - $start_time;
            $this->info("Execution time: " . round($execution_time, 2) . " seconds.");
        }
    }

    private function generateA4Page($cards, $page_count, $card_width, $card_height, $a4_width, $a4_height, $margin, $command_prefix)
    {
        $a4_file_name = "a4-page-{$page_count}.png";
        $a4_path = storage_path($a4_file_name);

        // Create a blank A4 image
        exec($command_prefix('convert')." -size {$a4_width}x{$a4_height} xc:white {$a4_path}");

        // Adjust margins for padding on left and right corners
        $horizontal_padding = 150; // Padding on left and right corners
        $adjusted_margin = $margin + $horizontal_padding;

        // Calculate card positions
        $rows = 5; // 5 rows of cards
        $cols = 2; // 2 columns of cards
        $x_spacing = ($a4_width - (2 * $adjusted_margin) - ($cols * $card_width)) / ($cols - 1);
        $y_spacing = ($a4_height - (2 * $margin) - ($rows * $card_height)) / ($rows - 1);

        foreach ($cards as $index => $card) {
            $row = intdiv($index, $cols);
            $col = $index % $cols;
            $x = $adjusted_margin + $col * ($card_width + $x_spacing);
            $y = $margin + $row * ($card_height + $y_spacing);

            // Add a border to the card
            $bordered_card = str_replace('.png', '-bordered.png', $card);
            exec($command_prefix('convert')." {$card} -bordercolor black -border 3x3 {$bordered_card}");

            // Overlay the card onto the A4 page
            exec($command_prefix('composite')." -geometry +{$x}+{$y} {$bordered_card} {$a4_path} {$a4_path}");

            // Optionally, delete the card and the bordered card
            unlink($card);
            unlink($bordered_card);
        }
    }
}

