<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create($this->getTableName(), function (Blueprint $table) {
            $table->id();
            $table->string('key', 191);
            $table->longText('value')->nullable();
            $table->text('description')->nullable();
            $table->string('type', 10)->default('string');

            // Polymorphic tenantable columns
            $table->nullableMorphs($this->getTenantableColumn());

            $table->timestamps();

            // Indexes for performance
            $table->index(['key'])->whereNull($this->getTenantableColumn() . '_type');
            $table->index([
                $this->getTenantableColumn() . '_type',
                $this->getTenantableColumn() . '_id',
                'key',
            ], 'idx_settings_tenant_key');

            // Unique constraint for key + tenant combination
            $table->unique([
                'key',
                $this->getTenantableColumn() . '_type',
                $this->getTenantableColumn() . '_id',
            ], 'settings_key_tenant_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists($this->getTableName());
    }

    private function getTableName(): string
    {
        return config('setanjo.table', 'settings');
    }

    private function getTenantableColumn(): string
    {
        return config('setanjo.tenantable_column', 'tenantable');
    }
};
