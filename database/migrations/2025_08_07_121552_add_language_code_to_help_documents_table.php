<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add language_code column first
        Schema::table('help_documents', function (Blueprint $table) {
            $table->string('language_code', 5)->default('en')->after('content');
        });
        
        // Try to drop the unique constraint on slug if it exists
        try {
            Schema::table('help_documents', function (Blueprint $table) {
                $table->dropUnique('help_documents_slug_unique');
            });
        } catch (\Exception $e) {
            // Ignore if the index doesn't exist
        }
        
        // Create a composite unique index
        Schema::table('help_documents', function (Blueprint $table) {
            $table->unique(['slug', 'language_code']);
        });
        
        // Migrate existing data - create a copy for each language with the appropriate content
        $documents = DB::table('help_documents')->get();
        
        foreach ($documents as $document) {
            $titleData = json_decode($document->title, true);
            $contentData = json_decode($document->content, true);
            
            // Skip if not JSON or already migrated
            if (!is_array($titleData) || !is_array($contentData)) {
                continue;
            }
            
            // Create English version (keep original record)
            DB::table('help_documents')
                ->where('id', $document->id)
                ->update([
                    'title' => $titleData['en'] ?? '',
                    'content' => $contentData['en'] ?? '',
                    'language_code' => 'en'
                ]);
            
            // Create Turkish version
            if (isset($titleData['tr']) && isset($contentData['tr'])) {
                // Clone the document but with Turkish content
                $newDocument = (array) $document;
                unset($newDocument['id']); // Remove ID to create a new record
                
                $newDocument['title'] = $titleData['tr'];
                $newDocument['content'] = $contentData['tr'];
                $newDocument['language_code'] = 'tr';
                
                DB::table('help_documents')->insert($newDocument);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Turkish versions
        DB::table('help_documents')->where('language_code', 'tr')->delete();
        
        Schema::table('help_documents', function (Blueprint $table) {
            $table->dropColumn('language_code');
        });
    }
};
