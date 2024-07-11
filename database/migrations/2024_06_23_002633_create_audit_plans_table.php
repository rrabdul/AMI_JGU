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
        Schema::create('audit_plans', function (Blueprint $table) {
            $table->id('id');
            $table->string('email');
            $table->string('no_phone');
            $table->unsignedBigInteger('auditee_id');
            $table->foreign('auditee_id')->references('id')->on('users');
            $table->dateTime('date_start');
            $table->dateTime('date_end');
            $table->unsignedBigInteger('audit_status_id');
            $table->foreign('audit_status_id')->references('id')->on('audit_statuses');
            $table->string('location_id')->nullable();
            $table->foreign('location_id')->references('id')->on('locations')->nullable()->onDelete('cascade');
            $table->unsignedBigInteger('department_id');
            $table->foreign('department_id')->references('id')->on('departments');
            $table->string('doc_path')->nullable();
            $table->string('link')->nullable();
            $table->string('remark_docs')->nullable();
            $table->string('periode')->nullable();
            $table->string('remark')->nullable();
            $table->string('type_audit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_plans');
    }
};