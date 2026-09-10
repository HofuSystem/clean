<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $washTypes = ['كوي فقط', 'غسيل فقط', 'غسيل وكوي', 'تنظيف جاف', 'wash_iron', 'wash_and_iron', 'iron_only', 'wash_only', 'dry_clean'];
        
        // Fetch clothes orders created since August 1, 2026, which have a note
        $orders = \Core\Orders\Models\Order::where('type', 'clothes')
            ->whereNotNull('note')
            ->where('created_at', '>=', '2026-08-01 00:00:00')
            ->get();

        foreach ($orders as $order) {
            $note = $order->note;
            $hasChanged = false;
            
            // If the note matches exactly or is combined with " - ", we want to strip the wash type
            // Split the note by " - " and filter out the wash types
            $parts = array_map('trim', explode(' - ', $note));
            $newParts = [];
            
            foreach ($parts as $part) {
                if (!in_array($part, $washTypes, true) && $part !== '') {
                    $newParts[] = $part;
                } else {
                    $hasChanged = true;
                }
            }
            
            // In case the note didn't have " - " but contained the word inside (e.g., from old logic)
            // Wait, the logic only appended with " - " or set exactly.
            // But let's be safe. If $hasChanged is true, update the note.
            if ($hasChanged) {
                $newNote = !empty($newParts) ? implode(' - ', $newParts) : null;
                $order->update(['note' => $newNote]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
